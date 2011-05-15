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
class IndexController extends Zend_Controller_Action
{
	protected $_finfo = null;

	public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $path = realpath(Zend_Registry::get('gallery_config')->imagepath);
		
		$dir = $this -> getRequest () -> getParam ('path');
		if ( ! $dir ){
			$dir = '';
		}

		$dir = realpath ($path . DIRECTORY_SEPARATOR . $dir);
	    if(0!==strpos($dir, $path)){
			throw new UnexpectedValueException('The given path is invalid');
		}	
		if ( ! $dir ) {
			throw new UnexpectedValueException('The given path could not be found');
		}
		$iterator = new DirectoryIterator($dir);
		$imgs = array ();
		$dirs = array ();
		foreach ( $iterator as $file) {
			if ( $file -> isDot()){
				continue;
			}
			if(0 === strpos($file->getFileName(),'.')){
				continue;
			}
			if($file->isDir()){
				$dirs[substr($file->getPathName(),strlen($path) + 1)] = $file -> getFileName ();
				continue;
			}
			if(!$this->_isImage($file)){
				continue;
			}
			$imgs[substr($file->getPathName(),strlen($path) + 1 )] = $file->getFileName();
		}
		$this->view->assign('directories', $dirs);
		$this->view->assign('images', $imgs);

    }

	protected function _isImage($file)
	{
		if(null == $this -> _finfo ) {
			$this -> _finfo = new Finfo ( FILEINFO_MIME_TYPE);
		}
		if(0!==strpos($this->_finfo->file($file->getPathName()),'image')){
			return false;
		}
		return true;
	}
}

