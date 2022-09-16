<?php

    namespace Illuminate\Console\View\Components;

    class Info
    {
        public function __construct(public $message)
        {
        }

        public function render()
        {
            if(is_scalar($this->message)) {
                try {
                    echo (string)$this->message;
                } catch (\Exception $e) {
                }
            }

            return $this->message;
        }
    }
