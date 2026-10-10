<?php
declare(strict_types=1);

class InfiniteCycler
{
    private ArrayIterator $source;

    /**
     * @param array<int|string, mixed> $items Исходные элементы.
     * @throws InvalidArgumentException Если массив пуст.
     */
    public function __construct(array $items)
    {
        if ($items === []) {
            throw new InvalidArgumentException('Массив не может быть пустым.');
        }
        $this->source = new ArrayIterator($items);
    }

    /**
     * @param int $count Сколько элементов выдать (> 0).
     * @return array<int, mixed>
     * @throws InvalidArgumentException Если $count <= 0.
     */
    public function take(int $count): array
    {
        if ($count <= 0) {
            throw new InvalidArgumentException('Количество элементов должно быть положительным.');
        }

        $infinite = new InfiniteIterator($this->source);
        $result = [];

        foreach ($infinite as $value) {
            $result[] = $value;
            if (count($result) >= $count) {
                break;
            }
        }

        return $result;
    }
}