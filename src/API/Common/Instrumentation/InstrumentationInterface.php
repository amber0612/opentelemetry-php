<?php

declare(strict_types=1);

namespace OpenTelemetry\API\Common\Instrumentation;

use OpenTelemetry\API\Metrics\MeterInterface;
use OpenTelemetry\API\Metrics\MeterProviderInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\TracerProviderInterface;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Instrumentation Interface is created to standardize 3rd party instrumentations (eg AWS SDK over in -contrib repo).
 * This interface along with InstrumentationTrait is meant as a base for instrumentations for theOpenTelemetry API.
 
 * A user of the instrumentation and API/SDK would use the call:

 *  $instrumentation = new Instrumentation;
 *  $instrumentation->activate() (Implemented in InstrumentationTrait)
 *  where Instrumentation is the class that implements the interface.

 *  to activate and use the instrumentation with the API/SDK.
 */
interface InstrumentationInterface
{
    public function getName(): string;

    public function getVersion(): ?string;

    public function getSchemaUrl(): ?string;

    public function init(): bool;

    public function activate(): bool;

    public function setPropagator(TextMapPropagatorInterface $propagator): void;

    public function getPropagator(): TextMapPropagatorInterface;

    public function setTracerProvider(TracerProviderInterface $tracerProvider): void;

    public function getTracerProvider(): TracerProviderInterface;

    public function getTracer(): TracerInterface;

    public function setMeterProvider(MeterProviderInterface $meterProvider): void;

    public function getMeter(): MeterInterface;

    public function setLogger(LoggerInterface $logger): void;

    public function getLogger(): LoggerInterface;
}
