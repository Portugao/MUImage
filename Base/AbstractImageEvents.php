<?php
/**
 * Image.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace MU\ImageModule\Base;

/**
 * Events definition base class.
 */
abstract class AbstractImageEvents
{
    /**
     * The muimagemodule.album_post_load event is thrown when albums
     * are loaded from the database.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const ALBUM_POST_LOAD = 'muimagemodule.album_post_load';
    
    /**
     * The muimagemodule.album_pre_persist event is thrown before a new album
     * is created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const ALBUM_PRE_PERSIST = 'muimagemodule.album_pre_persist';
    
    /**
     * The muimagemodule.album_post_persist event is thrown after a new album
     * has been created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const ALBUM_POST_PERSIST = 'muimagemodule.album_post_persist';
    
    /**
     * The muimagemodule.album_pre_remove event is thrown before an existing album
     * is removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const ALBUM_PRE_REMOVE = 'muimagemodule.album_pre_remove';
    
    /**
     * The muimagemodule.album_post_remove event is thrown after an existing album
     * has been removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const ALBUM_POST_REMOVE = 'muimagemodule.album_post_remove';
    
    /**
     * The muimagemodule.album_pre_update event is thrown before an existing album
     * is updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const ALBUM_PRE_UPDATE = 'muimagemodule.album_pre_update';
    
    /**
     * The muimagemodule.album_post_update event is thrown after an existing new album
     * has been updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAlbumEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const ALBUM_POST_UPDATE = 'muimagemodule.album_post_update';
    
    /**
     * The muimagemodule.picture_post_load event is thrown when pictures
     * are loaded from the database.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const PICTURE_POST_LOAD = 'muimagemodule.picture_post_load';
    
    /**
     * The muimagemodule.picture_pre_persist event is thrown before a new picture
     * is created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const PICTURE_PRE_PERSIST = 'muimagemodule.picture_pre_persist';
    
    /**
     * The muimagemodule.picture_post_persist event is thrown after a new picture
     * has been created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const PICTURE_POST_PERSIST = 'muimagemodule.picture_post_persist';
    
    /**
     * The muimagemodule.picture_pre_remove event is thrown before an existing picture
     * is removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const PICTURE_PRE_REMOVE = 'muimagemodule.picture_pre_remove';
    
    /**
     * The muimagemodule.picture_post_remove event is thrown after an existing picture
     * has been removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const PICTURE_POST_REMOVE = 'muimagemodule.picture_post_remove';
    
    /**
     * The muimagemodule.picture_pre_update event is thrown before an existing picture
     * is updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const PICTURE_PRE_UPDATE = 'muimagemodule.picture_pre_update';
    
    /**
     * The muimagemodule.picture_post_update event is thrown after an existing new picture
     * has been updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterPictureEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const PICTURE_POST_UPDATE = 'muimagemodule.picture_post_update';
    
    /**
     * The muimagemodule.avatar_post_load event is thrown when avatars
     * are loaded from the database.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const AVATAR_POST_LOAD = 'muimagemodule.avatar_post_load';
    
    /**
     * The muimagemodule.avatar_pre_persist event is thrown before a new avatar
     * is created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const AVATAR_PRE_PERSIST = 'muimagemodule.avatar_pre_persist';
    
    /**
     * The muimagemodule.avatar_post_persist event is thrown after a new avatar
     * has been created in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const AVATAR_POST_PERSIST = 'muimagemodule.avatar_post_persist';
    
    /**
     * The muimagemodule.avatar_pre_remove event is thrown before an existing avatar
     * is removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const AVATAR_PRE_REMOVE = 'muimagemodule.avatar_pre_remove';
    
    /**
     * The muimagemodule.avatar_post_remove event is thrown after an existing avatar
     * has been removed from the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const AVATAR_POST_REMOVE = 'muimagemodule.avatar_post_remove';
    
    /**
     * The muimagemodule.avatar_pre_update event is thrown before an existing avatar
     * is updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const AVATAR_PRE_UPDATE = 'muimagemodule.avatar_pre_update';
    
    /**
     * The muimagemodule.avatar_post_update event is thrown after an existing new avatar
     * has been updated in the system.
     *
     * The event listener receives an
     * MU\ImageModule\Event\FilterAvatarEvent instance.
     *
     * @see MU\ImageModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const AVATAR_POST_UPDATE = 'muimagemodule.avatar_post_update';
    
}
