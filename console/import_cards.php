<?php
/**
 * Pure PHP card importer for SQLite using global PDO
 */

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/database.php';

importCards();

function importCards()
{
    $pdo = db();

    $page = 1;
    $hasMore = true;

    while ($hasMore) {
        echo "Fetching page $page...\n";

        // --- Fetch API data ---
        $ch = curl_init("https://api.gatcg.com/cards/search?page=$page");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            throw new Exception("Failed to fetch API data.");
        }

        $response = json_decode($response, true);

        if (empty($response['data'])) {
            break;
        }

        foreach ($response['data'] as $cardData) {
            $lastUpdate = date('Y-m-d H:i:s', strtotime($cardData['last_update'] ?? 'now'));

            // --- Insert or update card ---
            $stmt = $pdo->prepare("
                INSERT INTO cards (uuid, element, name, slug, effect, flavor, cost_memory, cost_reserve, level, power, life, durability, speed, last_update)
                VALUES (:uuid, :element, :name, :slug, :effect, :flavor, :cost_memory, :cost_reserve, :level, :power, :life, :durability, :speed, :last_update)
                ON CONFLICT(uuid) DO UPDATE SET
                    element=excluded.element,
                    name=excluded.name,
                    slug=excluded.slug,
                    effect=excluded.effect,
                    flavor=excluded.flavor,
                    cost_memory=excluded.cost_memory,
                    cost_reserve=excluded.cost_reserve,
                    level=excluded.level,
                    power=excluded.power,
                    life=excluded.life,
                    durability=excluded.durability,
                    speed=excluded.speed,
                    last_update=excluded.last_update
            ");
            $stmt->execute([
                ':uuid' => $cardData['uuid'],
                ':element' => $cardData['element'],
                ':name' => $cardData['name'],
                ':slug' => $cardData['slug'],
                ':effect' => $cardData['effect'] ?? null,
                ':flavor' => $cardData['flavor'] ?? null,
                ':cost_memory' => $cardData['cost_memory'] ?? null,
                ':cost_reserve' => $cardData['cost_reserve'] ?? null,
                ':level' => $cardData['level'] ?? null,
                ':power' => $cardData['power'] ?? null,
                ':life' => $cardData['life'] ?? null,
                ':durability' => $cardData['durability'] ?? null,
                ':speed' => $cardData['speed'] ?? null,
                ':last_update' => $lastUpdate,
            ]);

            $cardId = getCardId($cardData['uuid']);

            // --- Link types ---
            if (!empty($cardData['types'])) {
                foreach ($cardData['types'] as $typeName) {
                    $typeId = insertOrGetId('types', $typeName);
                    linkPivot('card_types', $cardId, $typeId);
                }
            }

            // --- Link subtypes ---
            if (!empty($cardData['subtypes'])) {
                foreach ($cardData['subtypes'] as $subtypeName) {
                    $subtypeId = insertOrGetId('subtypes', $subtypeName);
                    linkPivot('card_subtypes', $cardId, $subtypeId);
                }
            }

            // --- Link classes ---
            if (!empty($cardData['classes'])) {
                foreach ($cardData['classes'] as $className) {
                    $classId = insertOrGetId('classes', $className);
                    linkPivot('card_classes', $cardId, $classId);
                }
            }

            // --- Editions ---
            if (!empty($cardData['editions'])) {
                foreach ($cardData['editions'] as $cardEdition) {
                    $editionLastUpdate = date('Y-m-d H:i:s', strtotime($cardEdition['last_update'] ?? 'now'));

                    $setId = null;
                    if (!empty($cardEdition['set'])) {
                        $editionSet = $cardEdition['set'];
                        $setLastUpdate = date('Y-m-d H:i:s', strtotime($editionSet['last_update'] ?? 'now'));

                        $stmt = $pdo->prepare("
                            INSERT INTO sets (name, prefix, language, last_update)
                            VALUES (:name, :prefix, :language, :last_update)
                            ON CONFLICT(name) DO UPDATE SET
                                prefix=excluded.prefix,
                                language=excluded.language,
                                last_update=excluded.last_update
                        ");
                        $stmt->execute([
                            ':name' => $editionSet['name'],
                            ':prefix' => $editionSet['prefix'] ?? null,
                            ':language' => $editionSet['language'] ?? null,
                            ':last_update' => $setLastUpdate,
                        ]);

                        $setId = getSetId($editionSet['name']);
                    }

                    $stmt = $pdo->prepare("
                        INSERT INTO editions (uuid, card_id, card_uuid, collector_number, slug, illustrator, rarity, flavor, last_update, set_id)
                        VALUES (:uuid, :card_id, :card_uuid, :collector_number, :slug, :illustrator, :rarity, :flavor, :last_update, :set_id)
                        ON CONFLICT(uuid) DO UPDATE SET
                            card_id=excluded.card_id,
                            card_uuid=excluded.card_uuid,
                            collector_number=excluded.collector_number,
                            slug=excluded.slug,
                            illustrator=excluded.illustrator,
                            rarity=excluded.rarity,
                            flavor=excluded.flavor,
                            last_update=excluded.last_update,
                            set_id=excluded.set_id
                    ");
                    $stmt->execute([
                        ':uuid' => $cardEdition['uuid'],
                        ':card_id' => $cardId,
                        ':card_uuid' => $cardEdition['card_id'],
                        ':collector_number' => $cardEdition['collector_number'],
                        ':slug' => $cardEdition['slug'],
                        ':illustrator' => $cardEdition['illustrator'] ?? null,
                        ':rarity' => $cardEdition['rarity'] ?? null,
                        ':flavor' => $cardEdition['flavor'] ?? null,
                        ':last_update' => $editionLastUpdate,
                        ':set_id' => $setId,
                    ]);
                }
            }
        }

        $page = $response['page'] + 1;
        $hasMore = $response['has_more'];
    }

    echo "Import completed successfully!\n";
}

// --- Helper functions using global $pdo ---

function getCardId($uuid) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM cards WHERE uuid = :uuid");
    $stmt->execute([':uuid' => $uuid]);
    return $stmt->fetchColumn();
}

function getSetId($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM sets WHERE name = :name");
    $stmt->execute([':name' => $name]);
    return $stmt->fetchColumn();
}

function insertOrGetId($table, $value) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM $table WHERE value = :value");
    $stmt->execute([':value' => $value]);
    $id = $stmt->fetchColumn();
    if ($id) return $id;

    $stmt = $pdo->prepare("INSERT INTO $table (value) VALUES (:value)");
    $stmt->execute([':value' => $value]);
    return $pdo->lastInsertId();
}

function linkPivot($table, $cardId, $foreignId) {
    global $pdo;

    $mapping = [
        'card_types' => 'type_id',
        'card_subtypes' => 'subtype_id',
        'card_classes' => 'class_id',
    ];

    $col = $mapping[$table] ?? null;
    if (!$col) {
        throw new Exception("No foreign column mapping for table $table");
    }

    $stmt = $pdo->prepare("
        INSERT OR IGNORE INTO $table (card_id, $col) 
        VALUES (:card_id, :foreign_id)
    ");
    $stmt->execute([':card_id' => $cardId, ':foreign_id' => $foreignId]);
}
