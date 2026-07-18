# Tasks: Lotto Hotpicks

**Status**: Migrated — all tasks completed

- [x] T001 Add `lotto-hotpicks` entry to `config/games.php`
- [x] T002 Create `LottoHotpicksGenerate` extending `LottoGenerate` in `app/Services/Lottery/LottoHotpicksGenerate.php`
- [x] T003 Map slug to `LottoDownload` in `Game::getDownloader()`
- [x] T004 Unit test `tests/Unit/Lottery/LottoHotpicksGenerateTest.php`
- [x] T005 Wire dispatch in `GameController::generateLottoHotpicks()`
