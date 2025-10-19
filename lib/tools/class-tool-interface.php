<?php

namespace Wp_Agents\Tools;

interface Tool_Interface {

	public function definition(): array;

	public function execute( array $arguments ): mixed;
}
