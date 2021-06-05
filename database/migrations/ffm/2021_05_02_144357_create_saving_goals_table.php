<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saving_goals', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->float('target_amount');
            $table->float('current_amount');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignID('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saving_goals');
    }
}
