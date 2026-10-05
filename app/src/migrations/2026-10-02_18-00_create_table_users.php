<?php declare(strict_types=1);

use App\Library\Database\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Database::schema()->create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('email', 255);
            $table->string('password_hash', 255);
            $table->string('name', 255);
            $table->timestamp('created_datetime');
            $table->timestamp('updated_datetime')->nullable()->default(null);

            // Уникальный индекс по емейлам
            $table->unique('email');
        });
    }

    public function down(): void {
        Database::schema()->dropIfExists('users');
    }
};
