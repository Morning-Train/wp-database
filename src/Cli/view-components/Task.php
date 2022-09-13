<?php

    namespace Illuminate\Console\View\Components;

    class Task
    {
        public function __construct(public $message)
        {
        }

        public function render()
        {
            try {
                echo (string)$this->message;
            } catch (\Exception $e) {
            }
            return $this->message;
        }
    }