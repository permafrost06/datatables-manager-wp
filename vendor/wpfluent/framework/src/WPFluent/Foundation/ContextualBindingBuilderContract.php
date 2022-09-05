<?php

namespace DtManager\Framework\Foundation;

interface ContextualBindingBuilderContract
{
    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract);
    
    /**
     * Define the implementation for the contextual binding.
     *
     * @param  Closure|string  $implementation
     * @return void
     */
    public function give($implementation);
}
