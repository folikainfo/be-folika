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
        Schema::create('advices', function (Blueprint $table) {
            $table->id();
            $table->string('prediction')->unique(); 
            $table->string('result');               
            $table->json('advice');                 
            $table->text('description');            
            $table->string('route')->nullable();                
            $table->timestamps();                   
        });
    }
};
