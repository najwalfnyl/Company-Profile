<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResetPasswordFieldsToAdminsTable extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('reset_password_token')->nullable()->after('photo');
            $table->timestamp('reset_password_expires')->nullable()->after('reset_password_token');
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('reset_password_token');
            $table->dropColumn('reset_password_expires');
        });
    }
}
