<?php declare(strict_types=1);

use App\Library\Database\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Database::schema()->create('boards', function (Blueprint $table) {
            $table->increments('board_id');
            $table->string('title', 255);
            $table->integer('owner_user_id');
            $table->boolean('is_public')->default(false);
            $table->timestamp('created_datetime');
            $table->timestamp('updated_datetime')->nullable()->default(null);
        });
    }

    public function down(): void {
        Database::schema()->dropIfExists('boards');
    }
};
