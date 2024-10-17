<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->string('status')->nullable(); // Menambahkan kolom status
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn('status'); // Menghapus kolom status jika migration dirollback
    });
}

};
