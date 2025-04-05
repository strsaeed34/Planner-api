<?php
// database/migrations/xxxx_xx_xx_create_goals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#3498db'); // Default color
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
};

