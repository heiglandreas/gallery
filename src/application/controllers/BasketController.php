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
 *
 * @category  Gallery
 * @package   Application
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license MIT-License
 * @version   GIT: $Revision: $
 * @since     15.05.2011
 */
class BasketController extends Zend_Controller_Action
{
	protected $_session = null;

    public function init()
	{
		$this->_session = new Zend_Session_Namespace('basket');
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->_helper->layout->disableLayout();
		$this->view->assign('basket',$this->getRequest()->getParam('basket',1));
		$this->view->assign('files',$this->_session->files);
    }

	public function addAction()
	{
		$basket=$this->getRequest()->getParam('id',1);
		$file=base64_decode($this->getRequest()->getParam('img'));
		$files=$this->_session->files;
		$files[$basket][$file]=basename($file);
		$this->_session->files=$files;
		$this->_redirect('basket/index/basket/' . $basket);
	}

	public function downloadAction()
	{
		$basket=$this->getRequest()->getParam('id',1);
		$files=$this->_session->files;
		$files=$files[$basket];
		$zip=new ZipArchive();
		$zipfile = tempnam(null,'gallery');
		if(!$zip->open($zipfile)){
			$this->_forward('index');
			return false;
		}

		$path = realpath(Zend_Registry::get('gallery_config')->imagepath);
		foreach($files as $file => $name){
			try{
			$dir = realpath ($path . DIRECTORY_SEPARATOR . $file);
        	if(0!==strpos($dir, $path)){
        		throw new UnexpectedValueException('The given path is invalid');
        	} 
        	if ( ! $dir ) {
            	throw new UnexpectedValueException('The given path could not be found');
        	}

			$zip->addFile($dir, $file);
			}catch(Exception $e){
				//
			}
		}
		$zip->close();
		$files=$this->_session->files;
		$files[$basket] = array ();
		$this->_session->files=$files;
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$this->getResponse()->setHeader('Content-Type','application/zip',true);
		$this->getResponse()->setHeader('Content-Length', filesize($zipfile),true);
		$this->getResponse()->setHeader('COntent-Disposition','attachment; filename='.date('Y-m-d H:i:s').'.zip',true);
		readfile($zipfile);
		unlink($zipfile);
	}

}

