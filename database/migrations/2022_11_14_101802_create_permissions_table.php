<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->foreignId('permissiontype_id')->references('id')->on(
                'permissiontypes'
            )->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            // "uid" Character varying NOT NULL,
            // "name" Character varying NOT NULL,
            // "permissiontype_id" Integer NOT NULL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
