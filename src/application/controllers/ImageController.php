<?php
/**
 * Copyright (c) 2011 Andreas Heigl<andreas@heigl.org
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 */
class ImageController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function showAction()
    {
		$base = realpath(Zend_Registry::get('gallery_config')->imagepath);
		$image = urldecode($this->getRequest()->getParam('path'));
	    $imagepath = realpath($base . DIRECTORY_SEPARATOR . $image);
		if(!$imagepath){
			throw new InvalidArgumentException('Path not found');
		}
		if(0!==strpos($imagepath,$base)){
			throw new InvalidArgumentException('Image-Path invalid');
		}
		$options=$this->getInvokeArg('bootstrap')->getOptions();
		$thumb = $options['tempDir'] . DIRECTORY_SEPARATOR . $image . '.png';
		if (!file_exists($thumb)){

			$folders=explode(DIRECTORY_SEPARATOR,dirname($image));
			$folderPath = $options['tempDir'];
			foreach ( $folders as $folder ){
				$folderPath .= DIRECTORY_SEPARATOR . $folder;
				if ( ! file_exists ( $folderPath ))	{
					mkdir($folderPath, 0777 );
				}
			}
			$string = '/usr/local/imagemagick/bin/convert "' . $imagepath . '" -thumbnail "200x200" -auto-orient "' . $thumb . '"';
			exec ( $string, $result );
		}
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		
		$this->getResponse()->setHeader('Content-Type','image/png'); 
		echo file_get_contents($thumb);
	}

	public function lightboxAction()
	{
		$base = realpath(Zend_Registry::get('gallery_config')->imagepath);
        $image = urldecode($this->getRequest()->getParam('path'));
        $imagepath = realpath($base . DIRECTORY_SEPARATOR . $image);
        if(!$imagepath){
        	throw new InvalidArgumentException('Path not found');
        }
        if(0!==strpos($imagepath,$base)){
            throw new InvalidArgumentException('Image-Path invalid');
        }
	    $options=$this->getInvokeArg('bootstrap')->getOptions();
	    $thumb = $options['tempDir'] . DIRECTORY_SEPARATOR . $image . '.600.png';
	    if (!file_exists($thumb)){
	    	$folders=explode(DIRECTORY_SEPARATOR,dirname($image));
	        $folderPath = $options['tempDir'];
	        foreach ( $folders as $folder ){
	        	$folderPath .= DIRECTORY_SEPARATOR . $folder;
	            if ( ! file_exists ( $folderPath )) {
	            	mkdir($folderPath, 0777 );
	            }
	        }
	        $string = '/usr/local/imagemagick/bin/convert "' . $imagepath . '" -thumbnail "600x600" -auto-orient "' . $thumb . '"';
	        exec ( $string, $result );
	    }
	    $this->_helper->viewRenderer->setNoRender();
	    $this->_helper->layout->disableLayout();
	 
	
	    $this->getResponse()->setHeader('Content-Type','image/png');
	    echo file_get_contents($thumb);
	}


}



