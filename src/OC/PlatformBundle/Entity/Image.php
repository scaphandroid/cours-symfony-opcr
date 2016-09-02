<?php
// src/OC/PlatformBundle/Entity/Image

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oc_image")
 * @ORM\Entity
 */
class Image
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="url", type="string", length=255)
   */
  private $url;

  /**
   * @ORM\Column(name="alt", type="string", length=255)
   */
  private $alt;
  
  private $file;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param string $alt
   */
  public function setAlt($alt)
  {
    $this->alt = $alt;
  }

  /**
   * @return string
   */
  public function getAlt()
  {
    return $this->alt;
  }
  
  public function getFile(){
	  return $this->file;
  }
  
  public function setFile(UploadedFile $file = null){
	  $this->file = $file;
  }
  
  public function upload(){
	  
	  if (null === $this->file) {
		  return;
	  }
	  
	  $name = $this->file->getClientOriginalName();
	  $this->file->move($this->getUploadRootDir(), $name);
	  
	  $this->url = $name;
	  
	  $this->alt = $name;
  }
  
  public function getUploadDir(){
	  return 'uploads/img';
  }
  
  protected function getUploadRootDir(){
	  return _DIR_.'/../../../../web/'.$this->getUploadDir();
  }
}
