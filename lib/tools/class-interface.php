<?php

interface Wp_Agents_Tools_Interface {

	public function definition(): array;

	public function execute( array $arguments ): mixed;
}
