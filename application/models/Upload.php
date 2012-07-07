<?php

class Model_Upload
{
        static function uploadFiles($uploadDir = null, $renameFile = true , $allowedExtensions = array(), $id = "x")
        {
            //Caso o diretorio não exista , cria o diretorio para o destino
            if(!file_exists($uploadDir))
                    mkdir($uploadDir, 0777, true);

            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->setDestination($uploadDir);

            $files = $adapter->getFileInfo();

            //Pega o nome dos campos
            $fields = array_keys($files);

            //Variavel contendo tipos
            $i= 0;

            foreach($files as $info)
            {
                $filename = strtolower($info['name']) ;
                $exts = split("[/\\.]", $filename) ;
                $n = count($exts)-1;
                $exts = $exts[$n];



                    $ext = $exts;
                    if($info['name'] != '')
                    {
                            //verifica se foi passado os tipos de arquivos permitidos no parametro
                            if(count($allowedExtensions) > 0)
                            {
                                    //verifica se a extenção enviada é uma extenção valida
                                        if(!array_keys($allowedExtensions, $ext))
                                        {
                                        return null;
                                        }
                            }
                    //Renomeia o Arquivo
                    //Caso o arquivo tenha sido carregado, insere o nome do arquivo ao array
                    //Ele tendo sido sobescrito ou não

                    if($renameFile)
                    {
                            $fileName = $id."_".time().".".$ext;
                            $adapter->addFilter('Rename', array('target' => $uploadDir.DIRECTORY_SEPARATOR.$fileName,'overwrite' => true));
                            $filesUploaded[] = $fileName;
                    }
                    else
                        $filesUploaed[] = $info['name'];

                        $adapter->receive($info['name']);
            }
        $i++;
       }

       return $filesUploaded;
     }
}

