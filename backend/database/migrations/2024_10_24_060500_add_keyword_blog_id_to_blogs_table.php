<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
            public function up(): void
        {
            Schema::table('blogs', function (Blueprint $table) {
                $table->foreignId('keyword_blog_id')->nullable()->after('id')->constrained('keyword_blogs')->onDelete('cascade');

            });
        }

        public function down(): void
        {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropForeign(['keyword_blog_id']);
                $table->dropColumn('keyword_blog_id');
            });
        }

};
