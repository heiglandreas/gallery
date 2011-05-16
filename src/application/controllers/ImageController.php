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

/**
 * The Image-Controller
 *
 * This controller handles every request associated with images.
 * Currently these are mainly the requests for retrieving the preview-
 * images
 *
 * @category  Gallery
 * @package   Application
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license MIT-License
 * @version   GIT: $Revision: $
 * @since     15.05.2011
 */
class ImageController extends Zend_Controller_Action
{
    
    /**
     * Get the requested image in size 200x200 pixel
     *
     * @return void
     */
    public function showAction()
    {
        $this -> _getImage('200');
    }
    
    /**
     * Get the requested image in size 600x600 pixel
     *
     * @return void
     */
    public function lightboxAction()
    {
        $this->_getImage('600');
    }
    
    /**
     * Render the requested image in the requested size
     *
     * @param int $size The size of the square the image has to fit into
     *
     * @return void
     */
    protected function _getImage($size)
    {
        $base = realpath(Zend_Registry::get('gallery_config')->imagepath);
        $imagemagick = realpath(Zend_Registry::get('gallery_config')->imagemagick_path
                     . DIRECTORY_SEPARATOR
                     . 'convert');
        if ( ! $imagemagick ){
            throw new UnexpectedValueException('No path to imagemagick given');
        }
        $image = urldecode($this->getRequest()->getParam('path'));
        $imagepath = realpath($base . DIRECTORY_SEPARATOR . $image);
        if(!$imagepath){
            throw new InvalidArgumentException('Path not found');
        }
        if(0!==strpos($imagepath,$base)){
            throw new InvalidArgumentException('Image-Path invalid');
        }
        $options=$this->getInvokeArg('bootstrap')->getOptions();
        $thumb = $options['tempDir'] . DIRECTORY_SEPARATOR . $image . '.' . $size . '.png';
        if (!file_exists($thumb)){

            $folders=explode(DIRECTORY_SEPARATOR,dirname($image));
            $folderPath = $options['tempDir'];
            foreach ( $folders as $folder ){
                $folderPath .= DIRECTORY_SEPARATOR . $folder;
                if ( ! file_exists ( $folderPath )) {
                    mkdir($folderPath, 0777 );
                }
            }
            $arguments = array ();
            $arguments[] = $imagemagick;
            $arguments[] = '"' . $imagepath . '"';
            $arguments[] = '-thumbnail';
            $arguments[] = '"' . $size . 'x' . $size . '"';
            $arguments[] = '-auto-orient';
            $arguments[] = '"' . $thumb . '"';
            exec ( implode(' ',$arguments));
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        
        $this->getResponse()->setHeader('Content-Type','image/png'); 
        readfile($thumb);
    }
}



