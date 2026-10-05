<?php declare(strict_types=1);

use App\Library\Database\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Database::schema()->create('cards', function (Blueprint $table) {
            $table->increments('card_id');
            $table->integer('board_id');
            $table->integer('column_id');
            $table->smallInteger('sort_index');
            $table->string('title', 255);
            $table->text('content')->nullable()->default(null);
            $table->string('color', 6)->nullable()->default(null);
            $table->boolean('is_archived')->default(false);
            $table->timestamp('created_datetime');
            $table->timestamp('updated_datetime')->nullable()->default(null);
        });

        // Парциальный индекс только по активным карточкам
        Database::statement('CREATE INDEX idx_active_board_id ON cards (board_id) WHERE is_archived = false');
    }

    public function down(): void {
        Database::schema()->dropIfExists('cards');
    }
};
