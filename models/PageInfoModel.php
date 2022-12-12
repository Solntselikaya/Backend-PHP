<?php
class PageInfoModel {
    private int $size;
    private int $count;
    private int $current;
    const maxSize = 6;

    public function __construct($size, $index) {
        $this->current = $index;
        $this->setSize($size);
        $this->setCount($size);
    }

    private function setSize($size) {
        $diff = $size - ($this->current - 1) * self::maxSize;
        if ($diff < self::maxSize) {
            $this->size = $diff;
        }
        else {
            $this->size = self::maxSize;
        }
    }

    private function setCount($size) {
        $this->count = floor($size / self::maxSize) + ($size % self::maxSize > 0);
    }

    public function getContents() {
        $r = array();
        foreach(get_object_vars($this) as $key => $value) {
            $r[$key] = $value;
        }

        return $r;
    }

}

?>