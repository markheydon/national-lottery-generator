# Tasks: EuroMillions Hotpicks

**Status**: Migrated — all tasks completed

- [x] T001 Add `euromillions-hotpicks` entry to `config/games.php`
- [x] T002 Create `EuromillionsHotpicksGenerate` extending `EuromillionsGenerate` in `app/Services/Lottery/EuromillionsHotpicksGenerate.php`
- [x] T003 Map slug to `EuromillionsDownload` in `Game::getDownloader()`
- [x] T004 Unit test `tests/Unit/Lottery/EuromillionsHotpicksGenerateTest.php`
- [x] T005 Wire dispatch in `GameController::generateEuroMillionsHotpicks()`
