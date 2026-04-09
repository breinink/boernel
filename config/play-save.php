<?php
/*
 * play-save.php — sla een voltooid AI-spel op in de database
 *
 * Vereist eerst de volgende tabel:
 *
 * CREATE TABLE `ai_games` (
 *   `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
 *   `played_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   `game_id`      BIGINT UNSIGNED NOT NULL COMMENT 'JS timestamp ms',
 *   `hands_played` TINYINT      NOT NULL DEFAULT 0,
 *   `score_wij`    SMALLINT     NOT NULL DEFAULT 0,
 *   `score_zij`    SMALLINT     NOT NULL DEFAULT 0,
 *   `winner`       ENUM('wij','zij','draw') NOT NULL DEFAULT 'draw',
 *   `ai_level`     TINYINT      NOT NULL DEFAULT 1,
 *   `detail_json`  JSON         NULL COMMENT 'Hand-voor-hand log',
 *   PRIMARY KEY (`id`),
 *   INDEX `idx_played_at` (`played_at`),
 *   INDEX `idx_game_id`   (`game_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
requireToken();

header('Content-Type: application/json');

$gameId      = (int)($_POST['game_id']    ?? 0);
$handsPlayed = (int)($_POST['hands']      ?? 0);
$scoreWij    = (int)($_POST['score_wij']  ?? 0);
$scoreZij    = (int)($_POST['score_zij']  ?? 0);
$aiLevel     = (int)($_POST['ai_level']   ?? 1);
$detailJson  = isset($_POST['detail_json']) ? (string)$_POST['detail_json'] : null;

if ($gameId <= 0 || $handsPlayed < 1 || $handsPlayed > 200) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Ongeldige invoer']);
    exit;
}

$winner = $scoreWij > $scoreZij ? 'wij' : ($scoreZij > $scoreWij ? 'zij' : 'draw');

// Valideer JSON indien aanwezig
if ($detailJson !== null) {
    $decoded = json_decode($detailJson, true);
    if ($decoded === null) {
        $detailJson = null; // ongeldige JSON negeren
    }
}

$con = dbConnect();

$stmt = $con->prepare(
    "INSERT INTO `ai_games`
     (`game_id`, `hands_played`, `score_wij`, `score_zij`, `winner`, `ai_level`, `detail_json`)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('iiiisis', $gameId, $handsPlayed, $scoreWij, $scoreZij, $winner, $aiLevel, $detailJson);
$stmt->execute();
$insertId = $stmt->insert_id;
$stmt->close();
$con->close();

echo json_encode(['ok' => true, 'id' => $insertId]);
