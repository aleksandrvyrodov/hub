<?php

$Series = new Clarice([2, 3, 5, 7, 11]);
$x = $Series->mashCompositionLine();
// print_r($Series->getSeriesMash());
print_r($x);


class Clarice
{

    private ?int $N;
    private int $K = 0;
    private ?array $series;
    private array $series_mash = array();
    private array $series_synthetic = array();

    public function __construct($series = array(0))
    {
        $this->createMesh($series);
    }

    public function createMesh(array $series): array
    {
        $this->N = count($series);
        if ($this->N < 1) return false;

        $this->series = $series;
        for ($this->K = 1; $this->K <= $this->N; $this->K++) {
            $this->series_mash[] = $this->BuildCombination();
        }
        return $this->getSeriesMash();
    }

    public function getSeriesMash()
    {
        return $this->series_mash;
    }

    public function mashComposition()
    {
        $lv1 = array();
        foreach ($this->series_mash as $chunk) {
            foreach ($chunk as $val) {
                $lv2 = $val[0];
                $count = count($val);
                for ($i = 1; $i < $count; $i++) {
                    $lv2 = $lv2 * $val[$i];
                }
                $lv1[] = $lv2;
            }
        }
        return $lv1;
    }

    public function mashCompositionLine()
    {
        $lv1 = array();
        foreach ($this->series_mash as $chunk) {
            foreach ($chunk as $val) {
                $lv2 = $val[0];
                $count = count($val);
                for ($i = 1; $i < $count; $i++) {
                    $lv2 = $lv2 .'-'. $val[$i];
                }
                $lv1[] = $lv2;
            }
        }
        return $lv1;
    }

    private function NextSet(): array
    {
        for ($i = $this->K - 1; $i >= 0; --$i) {
            if ($this->series_synthetic[$i] < $this->N - $this->K + $i + 1) {
                ++$this->series_synthetic[$i];
                for ($j = $i + 1; $j < $this->K; ++$j) {
                    $this->series_synthetic[$j] = $this->series_synthetic[$j - 1] + 1;
                }
                return $this->series_synthetic;
            }
        }
        return [];
    }

    private function ReplaceValues($original): array
    {
        $original = array_slice($original, 0, $this->K);
        foreach ($original as &$val) {
            $val = $this->series[$val - 1];
        }

        return array_pad($original, $this->N, 1);
        // return array_merge($original, array_fill(0, $this->N - $this->K, 1));
    }

    private function BuildCombination(): array
    {
        for ($i = 0; $i < $this->N; $i++) {
            $this->series_synthetic[$i] = $i + 1;
        }

        $buf[] = $this->ReplaceValues($this->series_synthetic);

        if ($this->N >= $this->K && $this->K != 0) {
            while ($original = $this->NextSet()) {
                $buf[] = $this->Replacevalues($original);
            }
        }

        return $buf;
    }
}
