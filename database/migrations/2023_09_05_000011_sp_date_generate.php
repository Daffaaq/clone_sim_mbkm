<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SPDateGenerate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP procedure IF EXISTS sp_date_generate");
        $procedure = "CREATE PROCEDURE sp_date_generate(IN v_endDate DATE)
BEGIN
	INSERT IGNORE s_date (tanggal, date_year, date_month, date_day, week_day)
	SELECT tanggal, year(tanggal), EXTRACT(YEAR_MONTH from tanggal), day(tanggal), (weekday(tanggal) + 1)
	FROM   (
		SELECT ADDDATE('2022-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i) AS tanggal
		FROM (SELECT
             0 i UNION SELECT
             1 UNION SELECT
             2 UNION SELECT
             3 UNION SELECT
             4 UNION SELECT
             5 UNION SELECT
             6 UNION SELECT
             7 UNION SELECT
             8 UNION SELECT
             9) t0,
         (SELECT
             0 i UNION SELECT
             1 UNION SELECT
             2 UNION SELECT
             3 UNION SELECT
             4 UNION SELECT
             5 UNION SELECT
             6 UNION SELECT
             7 UNION SELECT
             8 UNION SELECT
             9) t1,
         (SELECT
             0 i UNION SELECT
             1 UNION SELECT
             2 UNION SELECT
             3 UNION SELECT
             4 UNION SELECT
             5 UNION SELECT
             6 UNION SELECT
             7 UNION SELECT
             8 UNION SELECT
             9) t2,
         (SELECT
             0 i UNION SELECT
             1 UNION SELECT
             2 UNION SELECT
             3 UNION SELECT
             4 UNION SELECT
             5 UNION SELECT
             6 UNION SELECT
             7 UNION SELECT
             8 UNION SELECT
             9) t3,
         (SELECT
             0 i UNION SELECT
             1 UNION SELECT
             2 UNION SELECT
             3 UNION SELECT
             4 UNION SELECT
             5 UNION SELECT
             6 UNION SELECT
             7 UNION SELECT
             8 UNION SELECT
             9) t4) v
		WHERE tanggal <= v_endDate;
END";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
