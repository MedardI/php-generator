<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\PhpGenerator;

use Nette;


/**
 * Class method.
 *
 * @property string|null $body
 */
final class Method
{
	use Nette\SmartObject;
	use Traits\FunctionLike;
	use Traits\NameAware;
	use Traits\VisibilityAware;
	use Traits\CommentAware;

	/** @var string|null */
	private $body = '';

	/** @var bool */
	private $static = false;

	/** @var bool */
	private $final = false;

	/** @var bool */
	private $abstract = false;


	/**
	 * @param  string|array  $method
	 * @return static
	 */
	public static function from($method): self
	{
		return (new Factory)->fromMethodReflection(Nette\Utils\Callback::toReflection($method));
	}


	public function __construct(string $name)
	{
		if (!Helpers::isIdentifier($name)) {
			throw new Nette\InvalidArgumentException("Value '$name' is not valid name.");
		}
		$this->name = $name;
	}


	public function __toString(): string
	{
		return Helpers::formatDocComment($this->comment . "\n")
			. ($this->abstract ? 'abstract ' : '')
			. ($this->final ? 'final ' : '')
			. ($this->visibility ? $this->visibility . ' ' : '')
			. ($this->static ? 'static ' : '')
			. 'function '
			. ($this->returnReference ? '&' : '')
			. $this->name
			. ($params = $this->parametersToString())
			. $this->returnTypeToString()
			. ($this->abstract || $this->body === null
				? ';'
				: (strpos($params, "\n") === false ? "\n" : ' ')
					. "{\n"
					. Nette\Utils\Strings::indent(ltrim(rtrim($this->body) . "\n"), 1)
					. '}');
	}


	/**
	 * @return static
	 */
	public function setBody(?string $code, array $args = null): self
	{
		$this->body = $args === null || $code === null ? $code : Helpers::formatArgs($code, $args);
		return $this;
	}


	public function getBody(): ?string
	{
		return $this->body;
	}


	/**
	 * @return static
	 */
	public function setStatic(bool $state = true): self
	{
		$this->static = $state;
		return $this;
	}


	public function isStatic(): bool
	{
		return $this->static;
	}


	/**
	 * @return static
	 */
	public function setFinal(bool $state = true): self
	{
		$this->final = $state;
		return $this;
	}


	public function isFinal(): bool
	{
		return $this->final;
	}


	/**
	 * @return static
	 */
	public function setAbstract(bool $state = true): self
	{
		$this->abstract = $state;
		return $this;
	}


	public function isAbstract(): bool
	{
		return $this->abstract;
	}
}
