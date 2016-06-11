<?php

/**
 * Class Token
 */
class Token
{
    const INTEGER = 'integer';
    const STRING = 'string';
    const OPERATION = 'operation';
    const OPENING_BRACE = 'opening_brace';
    const CLOSING_BRACE = 'closing_brace';

    /**
     * @var string|null
     */
    protected $type = null;

    /**
     * @var string|null
     */
    protected $value = null;

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     */
    public function appendValue($value)
    {
        $this->value .= $value;
    }
}
