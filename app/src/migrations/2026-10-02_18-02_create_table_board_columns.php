<?php declare(strict_types=1);

use App\Library\Database\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Database::schema()->create('board_columns', function (Blueprint $table) {
            $table->increments('column_id');
            $table->integer('board_id');
            $table->smallInteger('ordinal_position');
            $table->string('title', 255);
            $table->string('color', 6)->nullable()->default(null);

            $table->index('board_id');
        });
    }

    public function down(): void {
        Database::schema()->dropIfExists('board_columns');
    }
};
