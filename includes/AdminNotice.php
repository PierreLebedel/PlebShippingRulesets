<?php

namespace PlebWooCommerceShippingRulesets;

class AdminNotice
{
	private $message;
	private $type;

	private $strong = false;
	private $dismissible = false;
	private $alt = false;

	public function __construct(string $message = '', string $type = 'info')
	{
		$this->setMessage($message);
		$this->setType($type);

		return $this;
	}

	public function setMessage(string $message = ''): self
	{
		$this->message = $message;
		return $this;
	}

	public function setType(string $type = 'info'): self
	{
		if (!in_array($type, ['info', 'success', 'warning', 'error'], true)) {
			$type = 'info';
		}

		$this->type = $type;
		return $this;
	}

	public function setStrong(bool $strong = true): self
	{
		$this->strong = $strong;
		return $this;
	}

	public function setDismissible(bool $dismissible = true): self
	{
		$this->dismissible = $dismissible;
		return $this;
	}

	public function setAlt(bool $alt = true): self
	{
		$this->alt = $alt;
		return $this;
	}

	public function getHtml(): string
	{
		if ($this->strong) {
			$this->message = '<strong>'.$this->message.'</strong>';
		}

		$classes = [
			'notice',
			'notice-'.$this->type,
		];

		if ($this->dismissible) {
			$classes[] = 'is-dismissible';
		}

		if ($this->alt) {
			$classes[] = 'notice-alt';
		}

		return '<div class="'.implode(' ', $classes).'">
            <p>'.$this->message.'</p>
        </div>';
	}

	public function render(): void
	{
		echo $this->getHtml();
	}

	public function __invoke()
	{
		$this->render();
	}
}
