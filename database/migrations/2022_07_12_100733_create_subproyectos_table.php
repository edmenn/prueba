<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubproyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subproyectos', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();    // auto-increment and primary key
            $table->integer('proyecto_id');
            $table->string('nombre', 150);
            $table->string('descripcion', 255);
            $table->string('codigo', 12);
            $table->integer('costo');
            $table->smallInteger('estado_id');
            $table->integer('contratado');
            $table->timestamps();

            // INDEXES
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('estado_id')->references('id')->on('estados')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subproyectos');
    }
}
