<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace   Shopware\Models\Media;
use         Shopware\Components\Model\ModelEntity,
            Doctrine\ORM\Mapping AS ORM;

/**
 * The Shopware album model is used to structure the media data.
 * <br>
 * The uploaded media is organized into albums.
 * Each album can have multiple child albums, which in turn may again involve media.
 * The Album has an album settings instance which contains the thumbnail configuration
 * and css icon class name.
 * <code>
 *   - Media    =>  Shopware\Models\Media\Media     [1:n] [s_media]
 *   - Album    =>  Shopware\Models\Media\Album     [1:n] [s_media_album]
 *   - Settings =>  Shopware\Models\Media\Settings  [1:1] [s_media_album_settings]
 * </code>
 * The s_media_album table has the follows indices:
 * <code>
 *   - PRIMARY KEY (`id`)
 * </code>
 *
 * @category   Shopware
 * @package    Models_Media
 * @copyright  Copyright (c) 20, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 *
 * @ORM\Entity
 * @ORM\Table(name="s_media_album")
 * @ORM\HasLifecycleCallbacks
 */
class Album extends ModelEntity
{
    /**
     * Unique identifier
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Name of the album, displayed in the tree, used to filter the tree.
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * Id of the parent album
     * @var integer $parentId
     * @ORM\Column(name="parentID", type="integer", nullable=true)
     */
    private $parentId = null;

    /**
     * Position of the album to configure the display order
     * @var integer $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * An album can have multiple sub-albums.
     * @var
     * @ORM\OneToMany(targetEntity="\Shopware\Models\Media\Album", mappedBy="parent")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;

    /**
     * An album can only be subordinated to another album.
     * @var null|\Shopware\Models\Media\Album $parent
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Media\Album", inversedBy="children")
     * @ORM\JoinColumn(name="parentID", referencedColumnName="id")
     */
    private $parent = null;

    /**
     * An album can be assigned to multiple media.
     * @var
     * @ORM\OneToMany(targetEntity="\Shopware\Models\Media\Media", mappedBy="album")
     */
    private $media;

    /**
     * Settings of the album.
     * @var \Shopware\Models\Media\Settings
     *
     * @ORM\OneToOne(targetEntity="\Shopware\Models\Media\Settings", mappedBy="album", orphanRemoval=true, cascade={"persist"})
     */
    protected $settings;

    /**
     * Initials the children and media collection
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media    = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Returns the identifier id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the album name
     * @param string $name
     * @return Album
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the album.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the position of the album
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position of the album.
     * @param int $position
     * @return \Shopware\Models\Media\Album
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Returns the child albums.
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the child albums.
     * @param  $children
     * @return array|\Shopware\Models\Media\Album
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Returns the parent album instance
     * @return null|\Shopware\Models\Media\Album
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent album instance
     * @param  $parent
     * @return \Shopware\Models\Media\Album
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Returns the associated media.
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the associated media
     * @param $media
     * @return void
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * Returns the album settings
     * @return \Shopware\Models\Media\Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the album settings
     * @param  $settings \Shopware\Models\Media\Settings
     * @return \Shopware\Models\Media\Album
     */
    public function setSettings(Settings $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Model lifecycle callback function, fired when the model is removed from the database.
     * All assigned media set to the unsorted album.
     * @ORM\PreRemove
     */
    public function onRemove()
    {
        //change the associated media to the unsorted album.
        $sql = "UPDATE s_media SET albumID = ? WHERE albumID = ?";
        Shopware()->Db()->query($sql, array(-10, $this->id));
    }

}
