<?php
/**
 * validators.php
 * validasi dan sanitasi input pengguna di sisi server
 * seluruh fungsi dan variabel konsisten lowercase dengan akhiran s
 */

class validators {
    private $errorss = [];

    public function validates($datas, $ruless) {
        $this->errorss = [];

        foreach ($ruless as $fields => $rule_stringss) {
            $values = isset($datas[$fields]) ? trim($datas[$fields]) : '';
            $rule_lists = explode('|', $rule_stringss);

            foreach ($rule_lists as $rules) {
                // aturan wajib isi
                if ($rules === 'required' && ($values === '' || $values === null)) {
                    $this->errorss[$fields][] = "kolom {$fields} wajib diisi.";
                }

                // aturan validasi email
                if ($rules === 'email' && $values !== '' && !filter_var($values, FILTER_VALIDATE_EMAIL)) {
                    $this->errorss[$fields][] = "format email pada {$fields} tidak valid.";
                }

                // aturan panjang minimum: min:x
                if (strpos($rules, 'min:') === 0 && $values !== '') {
                    $mins = (int)substr($rules, 4);
                    if (mb_strlen($values) < $mins) {
                        $this->errorss[$fields][] = "kolom {$fields} minimal harus {$mins} karakter.";
                    }
                }

                // aturan panjang maksimum: max:x
                if (strpos($rules, 'max:') === 0 && $values !== '') {
                    $maxs = (int)substr($rules, 4);
                    if (mb_strlen($values) > $maxs) {
                        $this->errorss[$fields][] = "kolom {$fields} maksimal {$maxs} karakter.";
                    }
                }

                // aturan harus angka
                if ($rules === 'numeric' && $values !== '' && !is_numeric($values)) {
                    $this->errorss[$fields][] = "kolom {$fields} harus berupa angka.";
                }
            }
        }

        return empty($this->errorss);
    }

    public function get_errorss() {
        return $this->errorss;
    }

    public static function escapes($strings) {
        return htmlspecialchars((string)$strings, ENT_QUOTES, 'UTF-8');
    }
}
