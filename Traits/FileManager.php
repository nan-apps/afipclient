<?php

namespace AfipServices\Traits;

use AfipServices\WSException;

trait FileManager
{

  private $temp_folder_path = TMP;
  private $resources_folder_path = '';

  /**
   * Devuelve la ruta a la carpeta de archivos temporales o al archivo temporal ingresado
   * @param string  $file_name
   * @param boolean $file_check si true, lanza exception cuando el archivo no existe
   * @return string
   * @throws WSException
   */
  protected function getTempFolderPath($file_name = null, $file_check = false)
  {

    $path = $this->temp_folder_path;

    if ($file_name) {
      $path .= $file_name;
      if ($file_check && !file_exists($path)) {
        throw new WSException("Archivo Temp/{$file_name} inexistente");
      }
    }
    return $path;
  }


  /**
   * Devuelve la ruta al archivo temporal ingresado
   * @param string  $file_name
   * @param boolean $file_check si true, lanza exception cuando el archivo no existe
   * @return string
   * @throws WSException
   */
  protected function getTempFilePath($file_name, $file_check = false)
  {
    return $this->getTempFolderPath($file_name, $file_check);
  }

  /**
   * Devuelve la ruta a la carpeta de recursos o al recurso ingresado
   * @param string  $file_name
   * @param boolean $file_check si true, lanza exception cuando el archivo no existe
   * @return string
   * @throws WSException
   */
  protected function getResourcesFolderPath($file_name = null, $file_check = false)
  {

    $path = $this->resources_folder_path;

    if ($file_name) {
      $path .= $file_name;
      if ($file_check && !file_exists($path)) {
        throw new WSException("Archivo Resources/{$file_name} inexistente");
      }
    }
    return $path;
  }

  /**
   * Devuelve la ruta al recurso ingresado
   * @param string  $file_name
   * @param boolean $file_check si true, lanza exception cuando el archivo no existe
   * @return string
   * @throws WSException
   */
  protected function getResourcesFilePath($file_name, $file_check = false)
  {

    return $this->getResourcesFolderPath($file_name, $file_check);
  }

  /**
   * Si la carpeta temporal no tiene permisos de escritura lanza excepcion
   * @throws WSException
   */
  protected function tempFolderPermissionsCheck()
  {
    if (!is_writable($this->getTempFolderPath())) {
      throw new WSException("La carpeta Temp debe tener permisos de escritura");
    };
  }

}