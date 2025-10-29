<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣ Rename table
        if (Schema::hasTable('batang_tubuh')) {
            Schema::rename('batang_tubuh', 'contents');
        }

        // 2️⃣ Rename columns
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                if (Schema::hasColumn('contents', 'batang_tubuh')) {
                    $table->renameColumn('batang_tubuh', 'contents');
                }
                if (Schema::hasColumn('contents', 'penjelasan')) {
                    $table->renameColumn('penjelasan', 'detil');
                }
            });
        }

        // 3️⃣ Rename foreign key in responds table
        if (Schema::hasTable('responds') && Schema::hasColumn('responds', 'batangtubuh_id')) {
            Schema::table('responds', function (Blueprint $table) {
                // Step A: drop foreign key jika namanya diketahui
                $sm = DB::select("SHOW CREATE TABLE responds");
                $fkName = null;

                foreach ($sm as $row) {
                    $createStmt = $row->{'Create Table'} ?? '';
                    if (preg_match('/CONSTRAINT `(.*?)` FOREIGN KEY \(`batangtubuh_id`\)/', $createStmt, $matches)) {
                        $fkName = $matches[1];
                        break;
                    }
                }

                if ($fkName) {
                    $table->dropForeign($fkName);
                }

                // Step B: rename column
                $table->renameColumn('batangtubuh_id', 'content_id');
            });

            // Step C: recreate FK
            Schema::table('responds', function (Blueprint $table) {
                $table->foreign('content_id')
                    ->references('id')
                    ->on('contents')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Reverse

        if (Schema::hasTable('responds') && Schema::hasColumn('responds', 'content_id')) {
            Schema::table('responds', function (Blueprint $table) {
                $sm = DB::select("SHOW CREATE TABLE responds");
                $fkName = null;

                foreach ($sm as $row) {
                    $createStmt = $row->{'Create Table'} ?? '';
                    if (preg_match('/CONSTRAINT `(.*?)` FOREIGN KEY \(`content_id`\)/', $createStmt, $matches)) {
                        $fkName = $matches[1];
                        break;
                    }
                }

                if ($fkName) {
                    $table->dropForeign($fkName);
                }

                $table->renameColumn('content_id', 'batangtubuh_id');
            });

            Schema::table('responds', function (Blueprint $table) {
                $table->foreign('batangtubuh_id')
                    ->references('id')
                    ->on('batangtubuh')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                if (Schema::hasColumn('contents', 'contents')) {
                    $table->renameColumn('contents', 'batang_tubuh');
                }
                if (Schema::hasColumn('contents', 'detil')) {
                    $table->renameColumn('detil', 'penjelasan');
                }
            });

            Schema::rename('contents', 'batangtubuh');
        }
    }
};
