<?php


/**
 * File cache driver
 */
class Doctrine_Cache_File extends Doctrine_Cache_Driver
{
    protected $_dir = '.';
    /**
     * constructor
     *
     * @param array $options    associative array of cache driver options
     */
    public function __construct($options = array())
    {
        if(isset($options['dir']) AND is_dir($options['dir']))
        {
            $this->_dir = $options['dir'];
        }
        else
        {
            throw new Doctrine_Cache_Exception('Cache-directory missing.');
        }

        parent::__construct($options);
    }



    protected function _getFile($id)
    {
        return $this->_dir . DIRECTORY_SEPARATOR . $id . '.doctrine_cache';
    }
    /**
     * Fetch a cache record from this cache driver instance
     *
     * @param string $id cache id
     * @param boolean $testCacheValidity        if set to false, the cache validity won't be tested
     * @return mixed  Returns either the cached data or false
     */
    protected function _doFetch($id, $testCacheValidity = true)
    {
    		$file = $this->_getFile($id);
    		if(!file_exists($file))
    			return false;

    		if($testCacheValidity AND filemtime($file) < time() - 3600*5)
    		{
    			unlink($file);
    			return false;
    		}

    		$data = file_get_contents($this->_getFile($id));
    		$data = @unserialize($data);

    		return $data;
    }

    /**
     * Test if a cache record exists for the passed id
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    protected function _doContains($id)
    {
    		$file = $this->_getFile($id);
    		return file_exists($file) ? filemtime($file) : false;
    }

    /**
     * Save a cache record directly. This method is implemented by the cache
     * drivers and used in Doctrine_Cache_Driver::save()
     *
     * @param string $id        cache id
     * @param string $data      data to cache
     * @param int $lifeTime     if != false, set a specific lifetime for this cache record (null => infinite lifeTime)
     * @return boolean true if no problem
     */
    protected function _doSave($id, $data, $lifeTime = false)
    {
		$file = $this->_getFile($id);
		return file_put_contents($file, serialize($data));
    }

    /**
     * Remove a cache record directly. This method is implemented by the cache
     * drivers and used in Doctrine_Cache_Driver::delete()
     *
     * @param string $id cache id
     * @return boolean true if no problem
     */
    protected function _doDelete($id)
    {
		$file = $this->_getFile($id);
		if(file_exists($file))
			return unlink($file);
    }
}