<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Config;

class DatabaseHelper
{
    /**
     * Creates a new database schema.

     * @param  string $schemaName The new schema name.
     * @return bool
     */
    public static function createDatabase($databaseName)
    {
        // We will use the `statement` method from the connection class so that
        // we have access to parameter binding.
        DB::statement('CREATE DATABASE ' . $databaseName);
    }

    public static function checkIsDatabaseExist($databaseName)
    {
        try {
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
            $db = DB::select($query, [$databaseName]);

            if (empty($db)) {
                return false;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            // return false;
            die("Could not connect to the database. Please check your configuration. error: " . $e->getMessage());
        }
    }

    public static function setDatabase($databaseName)
    {

        // Purge the current database connection, thus making Laravel get the default values all over again...
        DB::purge('mysql');

        // Now set the new connection
        config(['database.connections.mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $databaseName,
            'username' => 'root',
            'password' => ''
            // 'username' => env('DB_USERNAME', 'forge'),
            // 'password' => env('DB_PASSWORD', '')
        ]]);

        // ! Reconnect and close previous connection
        DB::reconnect('mysql');
    }

    public static function checkDatabaseConnection()
    {
        // Check if the database connection is working
        try {
            DB::connection()->getPDO();
            return json_encode(['connected' => true, "message" => "connected sucessfully to database " . DB::connection()->getDatabaseName()]);
        } catch (\Exception $e) {
            return json_encode(['connected' => false, "message" => "Could not connect to the database. Please check your configuration. error: " . $e->getMessage()]);
        }
    }

    public static function executeSqlFile()
    {
        // To run php artisan db:seed (Untuk menjalankan seeder)
        Artisan::call('db:seed');
    }

    public static function dumpTableStructure($databaseName)
    {
        //KONEKSI DATABASE
        $mysqlHostName      = '127.0.0.1';
        $mysqlUserName      = 'root';
        $mysqlPassword      = '';
        // $mysqlUserName      =  env('DB_USERNAME', 'forge');
        // $mysqlPassword      = env('DB_PASSWORD', '');
        $DbName             = $databaseName;

        // Membuat Koneksi ke Database
        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

        // Mengambil List Table dari Database
        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $tables = $statement->fetchAll();

        $structure = '';
        foreach ($tables as $table) {
            // Mengambil Struktur Table dari Database
            $show_table_query = "SHOW CREATE TABLE " . $table[0] . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach ($show_table_result as $show_table_row) {
                // Menyimpan Struktur Table ke dalam variabel
                if (isset($show_table_row["Create Table"])) {
                    $structure .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                }
            }
        }
        // Menyimpan Struktur Table ke dalam File
        $file_name = base_path() . '\database\DB_table_structure.sql';
        $file_handle = fopen($file_name, 'w + ');

        $output = $structure;
        fwrite($file_handle, $output);
        fclose($file_handle);
    }

    public static function populateDataMasterTable($previousDatabaseName, $newDatabaseName)
    {
        // List Table Datamaster
        $tables = array(
            'bidang',
            'cities',
            'countries',
            'kategori_penilaian_penyedia',
            'kode_akun_belanja_non_pengadaan',
            'level_eselon',
            'pejabat',
            'ppkom',
            'rekanan',
            'rekening',
            'skpd',
            'states',
            'target_kota',
            't_beban',
            't_jenis_pengadaan',
            't_keperluan',
            't_metode_pengadaan',
            't_metode_pengadaan_baru',
            't_operator_emonev',
            't_operator_sirup',
            't_sudah',
            't_tahun',
            'tb_comments',
            'tb_forms',
            'tb_groups',
            'tb_groups_access',
            'tb_jawaban_kuisioner',
            'tb_kategori_objek_penilaian',
            'tb_kuisioner',
            'tb_logs',
            'tb_menu',
            'tb_module',
            'tb_notification',
            'tb_pages',
            'tb_restapi',
            'tb_users',

        );

        foreach ($tables as $table) {
            // Query mengambil data dari table yang ada di database lama dan menyimpannya ke dalam database baru
            DB::statement(DB::raw("INSERT INTO " . $newDatabaseName . "." . $table . " SELECT * FROM " . $previousDatabaseName . "." . $table));
        }

        // Modify SKPD
        DB::statement(DB::raw("UPDATE skpd SET TOTAL_BL_MODAL = null,TOTAL_BL_BJ = null,TOTAL_BL_PEGAWAI = null,TOTAL_PAGU_SIPD = null,TOTAL_PAGU = null,REALISASI = null,PAGU_MURNI = null,PAGU_PAK = null,belanja_operasional = null,belanja_modal = null,belanja_tak_terduga = null,belanja_pengadaan = null,belanja_non_pengadaan = null,pagu_penyesuaian = null,pagu_keu_01 = null,pagu_keu_02 = null,pagu_keu_03 = null,pagu_keu_04 = null,pagu_keu_05 = null,pagu_keu_06 = null,pagu_keu_07 = null,pagu_keu_08 = null,pagu_keu_09 = null,pagu_keu_10 = null,pagu_keu_11 = null,pagu_keu_12 = null,realisasi_keu_01 = null,realisasi_keu_02 = null,realisasi_keu_03 = null,realisasi_keu_04 = null,realisasi_keu_05 = null,realisasi_keu_06 = null,realisasi_keu_07 = null,realisasi_keu_08 = null,realisasi_keu_09 = null,realisasi_keu_10 = null,realisasi_keu_11 = null,realisasi_keu_12 = null"));

        // Modify t_tahun
        DB::statement(DB::raw("UPDATE t_tahun SET tahun = " . substr($newDatabaseName, -4)));

        // Add New Year Data
        $lastYearData = DB::select("SELECT * FROM target_kota WHERE tahun = " . substr($previousDatabaseName, -4));
        $newYearData = (array)$lastYearData[0];
        $newYearData['id'] =  $newYearData['id'] + 1;
        $newYearData['tahun'] = substr($newDatabaseName, -4);
        DB::table('target_kota')->insert($newYearData);

        // Create View
        DB::statement(DB::raw('CREATE VIEW `view_paket_pengadaan_dientry` AS SELECT
		kode_rup as id,
		"Penyedia" as jenis_paket,
		mak,
		nama_paket,
		deskripsi,
		pagu_rup,
		nama_satker
		FROM
		rup_lkpp_penyedia
		where kode_rup IS NOT NULL
		UNION
		SELECT
		id_rup as id,
		"Swakelola" as jenis_paket,
		mak,
		Nama_Paket as nama_paket,
		Deskripsi as deskripsi,
		pagu as pagu_rup,
		nama_satker_sirup as nama_satker
		FROM
		rup_lkpp_swakelola 
		where id_rup IS NOT NULL'));
    }

    public static function createDb($cpanel_theme, $cPanelUser, $cPanelPass, $dbName)
    {
        $buildRequest = "/frontend/" . $cpanel_theme . "/sql/addb.html?db=" . $dbName;

        $openSocket = fsockopen('localhost', 2082);
        if (!$openSocket) {
            return "Socket error";
            exit();
        }

        $authString = $cPanelUser . ":" . $cPanelPass;
        $authPass = base64_encode($authString);
        $buildHeaders = "GET " . $buildRequest . "\r\n";
        $buildHeaders .= "HTTP/1.0\r\n";
        $buildHeaders .= "Host:localhost\r\n";
        $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
        $buildHeaders .= "\r\n";

        fputs($openSocket, $buildHeaders);
        while (!feof($openSocket)) {
            fgets($openSocket, 128);
        }
        fclose($openSocket);
    }

    public static function addUserToDb($cpanel_theme, $cPanelUser, $cPanelPass, $userName, $dbName, $privileges)
    {

        /* Redefine prefix for user and dbname */
        $prefix = substr($cPanelUser, 0, 8);

        $buildRequest = "/frontend/" . $cpanel_theme . "/sql/addusertodb.html?user=" . $prefix . "_" .
            $userName . "&db=" . $prefix . "_" . $dbName . "&privileges=" . $privileges;

        $openSocket = fsockopen('localhost', 2082);
        if (!$openSocket) {
            return "Socket error";
            exit();
        }

        $authString = $cPanelUser . ":" . $cPanelPass;
        $authPass = base64_encode($authString);
        $buildHeaders = "GET " . $buildRequest . "\r\n";
        $buildHeaders .= "HTTP/1.0\r\n";
        $buildHeaders .= "Host:localhost\r\n";
        $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
        $buildHeaders .= "\r\n";

        fputs($openSocket, $buildHeaders);
        while (!feof($openSocket)) {
            fgets($openSocket, 128);
        }
        fclose($openSocket);
    }
}
