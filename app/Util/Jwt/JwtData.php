<?php


namespace App\Util\Jwt;


class JwtData implements \ArrayAccess
{

    private $container = [];


    public function __construct($container = [])
    {
        $this->container = $container;
    }


    /**
     * 以对象的方式访问数组中的数据
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->container[$key];
    }

    /**
     * 以对象方式添加一个数组元素
     *
     * @param $key
     * @param $val
     */
    public function __set($key, $val)
    {
        $this->container[$key] = $val;
    }

    /**
     * 以对象方式判断数组元素是否设置
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->container[$key]);
    }

    /**
     * 以对象方式删除一个数组元素
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->container[$key]);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function toArray()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getIssuer()
    {
        return $this->container['iss'] ?? '';
    }

    /**
     * @param mixed $issuer
     */
    public function setIssuer($issuer): void
    {
        $this->container['iss'] = $issuer;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->container['scope'] ?? '';
    }

    /**
     * @param mixed $issuer
     */
    public function setScope($scope): void
    {
        $this->container['scope'] = $scope;
    }


    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->container['sub'] ?? '';
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject): void
    {
        $this->container['sub'] = $subject;

    }

    /**
     * @return mixed
     */
    public function getAudience()
    {
        return $this->container['aud'];
    }

    /**
     * @param mixed $audience
     */
    public function setAudience($audience): void
    {
        $this->container['aud'] = $audience;
    }

    /**
     * @return mixed
     */
    public function getExpiration()
    {
        return $this->container['exp'] ?? '';
    }

    /**
     * @param mixed $expiration
     */
    public function setExpiration($expiration): void
    {
        $this->container['exp'] = $expiration;
    }

    /**
     * @return mixed
     */
    public function getNotBefore()
    {
        return $this->container['nbf'] ?? '';
    }

    /**
     * @param mixed $notBefore
     */
    public function setNotBefore($notBefore): void
    {
        $this->container['nbf'] = $notBefore;
    }

    /**
     * @return mixed
     */
    public function getIssuedAt()
    {
        return $this->container['iat'] ?? '';
    }

    /**
     * @param mixed $issuedAt
     */
    public function setIssuedAt($issuedAt): void
    {
        $this->container['iat'] = $issuedAt;
    }

    /**
     * @return mixed
     */
    public function getJwtId()
    {
        return $this->container['jti'] ?? '';
    }

    /**
     * @param mixed $jwtId
     */
    public function setJwtId($jwtId): void
    {
        $this->container['jti'] = $jwtId;
    }

    /**
     * @return mixed
     */
    public function getJwtData()
    {
        return $this->container['data'] ?? [];
    }

    /**
     * @param mixed $jwtData
     */
    public function setJwtData(array $jwtData): void
    {
        $this->container['data'] = $jwtData;
    }


}