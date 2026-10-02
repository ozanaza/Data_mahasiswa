<?php
/**
 * Klien untuk Google Apps Script Web App yang terhubung ke Google Sheets.
 * Data dipertukarkan dalam format JSON melalui cURL.
 */
class GoogleSheets
{
    private string $url;
    private bool $verifySsl;

    public function __construct(array $config)
    {
        if (empty($config['script_url']) || strpos($config['script_url'], 'ISI_DEPLOYMENT_ID') !== false) {
            throw new Exception('URL Apps Script belum diisi pada config.php.');
        }
        $this->url       = $config['script_url'];
        $this->verifySsl = $config['verify_ssl'] ?? true;
    }

    private function call(?array $payload = null): array
    {
        $ch   = curl_init($payload === null ? $this->url . '?action=list' : $this->url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true, // Apps Script mengalihkan (redirect) ke server respons
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_TIMEOUT        => 30,
        ];
        if ($payload !== null) {
            $opts[CURLOPT_POST]       = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
            $opts[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        }
        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('cURL: ' . curl_error($ch));
        }
        curl_close($ch);

        $res = json_decode($raw, true);
        if (!is_array($res)) {
            throw new Exception('Respons bukan JSON. Pastikan Web App di-deploy dengan akses "Anyone" dan URL berakhiran /exec.');
        }
        if (($res['status'] ?? '') !== 'success') {
            throw new Exception($res['message'] ?? 'Terjadi kesalahan pada Apps Script.');
        }
        return $res;
    }

    public function getAll(): array
    {
        $res  = $this->call();
        $rows = [];
        foreach ($res['data'] as $r) {
            $rows[] = [
                'row'  => $r['row'],
                'nim'  => (string)$r['nim'],
                'nama' => (string)$r['nama'],
                'p1'   => (float)$r['poin1'],
                'p2'   => (float)$r['poin2'],
                'p3'   => (float)$r['poin3'],
            ];
        }
        return $rows;
    }

    public function findByNim(string $nim): ?array
    {
        foreach ($this->getAll() as $r) {
            if ($r['nim'] === $nim) {
                return $r;
            }
        }
        return null;
    }

    private function kirim(string $action, string $nim, string $nama = '', float $p1 = 0, float $p2 = 0, float $p3 = 0): void
    {
        $this->call([
            'action' => $action, 'nim' => $nim, 'nama' => $nama,
            'poin1'  => $p1, 'poin2' => $p2, 'poin3' => $p3,
        ]);
    }

    public function append(string $nim, string $nama, float $p1, float $p2, float $p3): void
    {
        $this->kirim('tambah', $nim, $nama, $p1, $p2, $p3);
    }

    public function update(string $nim, string $nama, float $p1, float $p2, float $p3): void
    {
        $this->kirim('ubah', $nim, $nama, $p1, $p2, $p3);
    }

    public function delete(string $nim): void
    {
        $this->kirim('hapus', $nim);
    }
}
