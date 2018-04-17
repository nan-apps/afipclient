<?php
namespace AfipClient\Utils;

use AfipClient\ACException;

class FileManager
{
    /**
     * Devuelve el contenido del archivo temporal ingresado
     * @param string $file_name
     * @param boolean $file_check si true, lanza exception cuando el archivo no existe
     * @return string|boolean
     * @throws ACException
     */
    public function getTempFileContent($file_name, $file_check = false)
    {
        return $this->getContent($this->getTempFolderPath($file_name, $file_check));
    }

    /**
     * Pone contenido en archivo temporal
     * @param string $file_name
     * @param string $content
     * @return int|boolean
     * @throws ACException
     */
    public function putTempFileContent($file_name, $content)
    {
        return $this->putContent($this->getTempFolderPath($file_name, false), $content);
    }

    /**
     * Devuelve la ruta al archivo temporal ingresado
     * @param string $file_name
     * @param boolean $file_check si true, lanza exception cuando el archivo no existe
     * @return string
     * @throws ACException
     */
    public function getTempFilePath($file_name, $file_check = false)
    {
        return $this->getTempFolderPath($file_name, $file_check);
    }

    /**
     * Devuelve la ruta a la carpeta de archivos temporales o al archivo temporal ingresado
     * @param string $file_name
     * @param boolean $file_check si true, lanza exception cuando el archivo no existe
     * @return string
     * @throws ACException
     */
    public function getTempFolderPath($file_name = null, $file_check = false)
    {
        $path = sys_get_temp_dir() . '/';

        if ($file_name) {
            $path .= $file_name;
            if ($file_check) {
                $this->validateFile($path, $file_name);
            }
        }
        return $path;
    }

    /**
     * Crea un fichero con un nombre de único en carpeta de Temps.
     * @param string $file_name
     * @return string|false
     */
    public function createUniqueTempFile($file_name)
    {
        return $this->createUnique($this->getTempFolderPath(), $file_name);
    }

    /**
     * Si la carpeta temporal no tiene permisos de escritura lanza excepcion
     * @throws ACException
     */
    public function tempFolderPermissionsCheck()
    {
        if (!is_writable($this->getTempFolderPath())) {
            throw new ACException("La carpeta Temp debe tener permisos de escritura");
        };
    }

    /**
     * Devuelve el contenido de un archivo. Wrapper file_get_content
     * @param string $file_path
     * @param boolean $file_check
     * @param string $file_name nombre de archivo para mostrar en error
     * @return string|boolean FALSE si hay error
     * @throws ACException
     */
    public function getContent($file_path, $file_check = false, $file_name = '')
    {
        if ($file_check) {
            $this->validateFile($file_path, $file_name);
        }

        if (file_exists($file_path)) {
            return file_get_contents($file_path);
        } else {
            return false;
        }
    }

    /**
    * Si el archivo no exite lanza excepcion
    * @param string $file_path
    * @param string $file_name
    * @throws ACException
    */
    public function validateFile($file_path, $file_name = 'Archivo')
    {
        if (!file_exists($file_path)) {
            throw new ACException("{$file_name} inexistente en {$file_path}");
        } else {
            return $file_path;
        }
    }

    /**
     * Pone contenido en un archivo. Wrapper file_put_contents
     * @param string $file_path
     * @param string $content
     * @return int|boolean
     */
    public function putContent($file_path, $content)
    {
        return file_put_contents($file_path, $content);
    }

    /**
     * Pone contenido en un archivo
     * @param SimpleXmlElement $xml
     * @param string $file_save_path
     * @return boolean
     */
    public function asXML(\SimpleXMLElement $xml, $file_save_path)
    {
        return $xml->asXML($file_save_path);
    }

    /**
     * Elimina los archivos pasados
     * @param Array $files
     */
    public function unlinkFiles(array $files)
    {
        array_map('unlink', $files);
    }

    /**
     * Crea un fichero con un nombre de fichero único. Wrapper tempnam
     * @param string $dir
     * @param string $file_name
     * @return string|false
     */
    public function createUnique($dir, $file_name)
    {
        return tempnam($dir, $file_name);
    }
}
