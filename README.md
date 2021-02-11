# plugin-emarking
Entrega prueba técnica

Solución:

Para resolver este problema, me percate que en la dirección "http://localhost/mod/emarking/print/exam.php?id=5" en la cual estaba la página a editar, estaba el directorio print y dentro este el archivo "exam.php". Luego, investigando en el archivo "exam.php" encontre el comentario "// Table header.", el cual me dió indicios sobre esa linea de texto. Posteriormente, me fijé en la función get_string y investigué sobre ella en la documentación oficial. En ella se menciona la existencia de un directorio llamado lang y un archivo llamado es/en, en donde cada string era definido apartir de un arreglo asociativo. Finalmente estando ahí, utilizé la función "Buscar" de VS Code para encontrar el mensaje y lo edité.





