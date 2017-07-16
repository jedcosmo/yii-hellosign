# Yii2 Upload Crop Widget
Widget que genera un campo en un formulario que almacena una imagen que enviar al servidor además de los valores para recordar esta imagen.

## ¿Qué es Upload Crop Widget?

Este widget añade un nuevo campo input que sirve para subir imágenes a través de formularios al servidor junto con los datos necesarios para poder recortarlas desde Yii2. Estos datos son generados a través de javaScript mediante un selector que el usuario podrá usar para tal efecto y que se mostrará a través de una ventana modal que se desplegará automáticamente al elegir una imagen en el campo de tipo input file.

Desarrollado por Joseba Juániz ([@Patroklo](http://twitter.com/Patroklo))

[Versión en inglés](https://github.com/Patroklo/yii2-widget-upload-crop/blob/master/README.md)

## Requisitos mínimos

* Yii2
* Php 5.4 o superior
* JQuery

## Planes futuros

* Ninguno.

## Licencia

Esto es software libre. Está liberado bajo los términos de la siguiente licencia BSD

Copyright (c) 2014, by Cyneek
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of Cyneek nor the names of its contributors
   may be used to endorse or promote products derived from this software
   without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER "AS IS" AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

## Instalación

* Instalar [Yii 2](http://www.yiiframework.com/download)
* Instalar el paquete vía [composer](http://getcomposer.org/download/) 
		
		"cyneek/yii2-widget-upload-crop": "dev-master"
		
* Profit!

## Funcionamiento

El widget incluirá por defecto todos el javaScript y los campos necesarios en el formulario para su funcionamiento. Una vez enviados los datos de este formulario al servidor será necesario obtener el fichero y los datos de recorte para poder aplicarlos al modelo de datos y la imagen en sí.

	echo \cyneek\yii2\widget\upload\crop\UploadCrop::widget(['form' => $form, 'model' => $model, 'attribute' => 'fieldName']);

Dado que esta librería es un campo widget separado del sistema de formularios en sí mismo existe el problema de que no se puede crear por defecto el campo de file input de forma normal incluyendo todas las opciones por defecto del formulario en el que se encuentra, al igual que los campos input normales de Yii2, esto afecta a validaciones desde cliente, modificaciones css o en php que se hayan realizado, etc.. Para solucionar esto se ha añadido una opción adicional en el widget que se encarga de recoger el formulario en el que se encuentra incluído el widget y lo usa para crear los campos internos que tiene y así adecuarlo al resto del diseño.

## Recoger los datos del formulario

El formulario retornará al servidor los datos de la siguiente manera:

* Archivo imagen: se deberá asignar al atributo del modelo de datos que se pasará en la lista de opciones.

* Valores de recorte: se retornarán por post en un array con la forma:


		["fieldName-cropping"]=>
		  array(4) {
			["x"]=>
				string(1) "12"
			["width"]=>
				string(3) "400"
			["y"]=>
				string(1) "0"
			["height"]=>
				string(3) "297"
		  }


## Opciones del método Widget

* model (string) (obligatorio)
> Define el modelo de datos que se usará para crear el campo del formulario.


* attribute (string) (obligatorio)
> Define el atributo del modelo de datos que se usará para crear el campo del formulario.


* form (ActiveForm) (opcional)
> Es el objeto Form que define el formulario en el que está incluído el widget. Se usará para heredar las configuraciones del formulario a la hora de crear el campo.


* enableClientValidation (boolean) (opcional)
> Usado cuando no se pasa el objeto formulario. Define si está activado o no en el widget la validación en navegador mediante javascript.


* imageOptions (array) (opcional)
> Listado de opciones que se introducirán en el campo imagen que se usará para cropear en la ventana modal. Debe estar en formato ["opcion" => "valor"].


* jcropOptions (array) (opcional)
> Listado con las opciones que se introducirán en la creación del crop en javascript. Para más información sobre las opciones que se pueden introducir consultar en [esta página](https://github.com/fengyuanchen/cropper#options).


* maxSize (integer) (opcional)
> Siendo por defecto 300, es el atributo max-height y max-width que se aplicará a la muestra de preview del recorte que se aplicará a la imagen.