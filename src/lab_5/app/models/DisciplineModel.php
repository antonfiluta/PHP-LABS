<?php
declare(strict_types=1);

/**
 * Модель учебных дисциплин. Работает с JSON- или XML-файлом
 * в зависимости от STORAGE_TYPE. Инкапсулирует всю работу с данными.
 */
class DisciplineModel
{
    private string $jsonFile;
    private string $xmlFile;
    private string $storageType;

    public function __construct()
    {
        $this->jsonFile = ROOT_DIR . '/data/disciplines.json';
        $this->xmlFile = ROOT_DIR . '/data/disciplines.xml';
        $this->storageType = STORAGE_TYPE;
    }

    public function getStorageType(): string
    {
        return $this->storageType;
    }

    /** Прочитать все записи из выбранного хранилища. */
    public function getAll(): array
    {
        return $this->storageType === 'json'
            ? $this->readJson()
            : $this->readXml();
    }

    /** Записать массив в выбранное хранилище. */
    public function saveAll(array $items): void
    {
        if ($this->storageType === 'json') {
            file_put_contents(
                $this->jsonFile,
                json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        } else {
            $this->writeXml($items);
        }
    }

    // ---------- JSON ----------
    private function readJson(): array
    {
        if (!file_exists($this->jsonFile))
            return [];
        $raw = file_get_contents($this->jsonFile);
        return json_decode($raw, true) ?? [];
    }

    // ---------- XML ----------
    private function readXml(): array
    {
        if (!file_exists($this->xmlFile))
            return [];
        $xml = simplexml_load_file($this->xmlFile);
        if ($xml === false)
            return [];

        $items = [];
        foreach ($xml->discipline as $d) {
            $items[] = [
                'id' => (int) $d->id,
                'name' => (string) $d->name,
                'teacher' => (string) $d->teacher,
                'semester' => (int) $d->semester,
                'hours' => (int) $d->hours,
                'faculty' => (string) $d->faculty,
            ];
        }
        return $items;
    }

    private function writeXml(array $items): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><disciplines/>');
        foreach ($items as $item) {
            $d = $xml->addChild('discipline');
            $d->addChild('id', (string) $item['id']);
            $d->addChild('name', htmlspecialchars((string) $item['name'], ENT_XML1));
            $d->addChild('teacher', htmlspecialchars((string) $item['teacher'], ENT_XML1));
            $d->addChild('semester', (string) $item['semester']);
            $d->addChild('hours', (string) $item['hours']);
            $d->addChild('faculty', htmlspecialchars((string) $item['faculty'], ENT_XML1));
        }
        $xml->asXML($this->xmlFile);
    }

    // ---------- CRUD ----------
    public function add(array $data): array
    {
        $items = $this->getAll();
        $newId = 1;
        foreach ($items as $item) {
            if ($item['id'] >= $newId)
                $newId = $item['id'] + 1;
        }
        $data['id'] = $newId;
        $items[] = $data;
        $this->saveAll($items);
        return $data;
    }

    public function update(int $id, string $field, mixed $value): bool
    {
        $allowed = ['name', 'teacher', 'semester', 'hours', 'faculty'];
        if (!in_array($field, $allowed, true))
            return false;

        $items = $this->getAll();
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                if (in_array($field, ['semester', 'hours'], true)) {
                    $value = (int) $value;
                }
                $item[$field] = $value;
                $found = true;
                break;
            }
        }
        unset($item);

        if ($found)
            $this->saveAll($items);
        return $found;
    }

    public function delete(int $id): bool
    {
        $items = $this->getAll();
        $before = count($items);
        $items = array_values(array_filter($items, fn($i) => $i['id'] !== $id));
        if (count($items) === $before)
            return false;
        $this->saveAll($items);
        return true;
    }

    // ---------- Фильтрация и сортировка ----------
    public function filter(array $items, ?int $semester, ?string $teacher): array
    {
        return array_values(array_filter($items, function ($item) use ($semester, $teacher) {
            if ($semester !== null && (int) $item['semester'] !== $semester)
                return false;
            if (
                $teacher !== null && $teacher !== ''
                && stripos((string) $item['teacher'], $teacher) === false
            )
                return false;
            return true;
        }));
    }

    public function sort(array $items, string $by = 'name', string $dir = 'asc'): array
    {
        $allowed = ['name', 'teacher', 'semester', 'hours', 'faculty'];
        if (!in_array($by, $allowed, true))
            $by = 'name';

        usort($items, function ($a, $b) use ($by, $dir) {
            if (is_numeric($a[$by]) && is_numeric($b[$by])) {
                $cmp = (int) $a[$by] <=> (int) $b[$by];
            } else {
                $cmp = strcasecmp((string) $a[$by], (string) $b[$by]);
            }
            return $dir === 'desc' ? -$cmp : $cmp;
        });
        return $items;
    }

    // ---------- Статистика ----------
    public function stats(array $items): array
    {
        $byFaculty = [];
        $totalHours = 0;
        $semesters = [];

        foreach ($items as $item) {
            $byFaculty[$item['faculty']] = ($byFaculty[$item['faculty']] ?? 0) + 1;
            $semesters[$item['semester']] = ($semesters[$item['semester']] ?? 0) + 1;
            $totalHours += (int) $item['hours'];
        }
        ksort($byFaculty);
        ksort($semesters);

        return [
            'total' => count($items),
            'hours' => $totalHours,
            'byFaculty' => $byFaculty,
            'bySemester' => $semesters,
        ];
    }

    // ---------- Переключение хранилища ----------
    public function switchStorage(string $type): void
    {
        if (!in_array($type, ['json', 'xml'], true))
            return;

        // Читаем из старого хранилища
        $items = $this->getAll();

        // Переключаем
        $_SESSION['storage_type'] = $type;
        $this->storageType = $type;

        // Пишем в новое
        $this->saveAll($items);
    }

    /** Если данных нет — наполняем демонстрационным набором. */
    public function seedIfEmpty(): void
    {
        if (count($this->getAll()) > 0)
            return;

        $demo = [
            ['id' => 1, 'name' => 'Высокоуровневые языки программирования', 'teacher' => 'Казюцич В.О.', 'semester' => 5, 'hours' => 72, 'faculty' => 'ФКСиС'],
            ['id' => 2, 'name' => 'Базы данных', 'teacher' => 'Пономарёва Е.И.', 'semester' => 5, 'hours' => 64, 'faculty' => 'ФКСиС'],
            ['id' => 3, 'name' => 'Веб-программирование', 'teacher' => 'Иванов И.И.', 'semester' => 6, 'hours' => 48, 'faculty' => 'ФКСиС'],
            ['id' => 4, 'name' => 'Дискретная математика', 'teacher' => 'Петров П.П.', 'semester' => 1, 'hours' => 90, 'faculty' => 'ФИТУ'],
            ['id' => 5, 'name' => 'Математический анализ', 'teacher' => 'Сидоров С.С.', 'semester' => 2, 'hours' => 108, 'faculty' => 'ФИТУ'],
        ];
        $this->saveAll($demo);
    }
}