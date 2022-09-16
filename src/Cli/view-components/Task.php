<?php

    namespace Illuminate\Console\View\Components;

    use Symfony\Component\Console\Output\OutputInterface;
    use Throwable;

    class Task
    {
        public function __construct(public $message)
        {
        }

        public function render($description, $task = null)
        {
            try {
                $task();
            } catch (Throwable $e) {
                throw $e;
            } finally {
                return $this->message;
            }
        }
    }
