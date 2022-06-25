<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use OpenTelemetry\API\Trace as API;
use OpenTelemetry\Context\Context;
use OpenTelemetry\Contrib\OtlpGrpc\Exporter as OTLPExporter;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SamplingResult;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

$sampler = new AlwaysOnSampler();
$samplingResult = $sampler->shouldSample(
    Context::getCurrent(),
    md5((string) microtime(true)),
    'io.opentelemetry.example',
    API\SpanKind::KIND_INTERNAL
);

$Exporter = new OTLPExporter();

if (SamplingResult::RECORD_AND_SAMPLE === $samplingResult->getDecision()) {
    echo 'Starting OTLPGrpcExample';
    $tracer = (new TracerProvider(new SimpleSpanProcessor($Exporter)))
        ->getTracer('io.opentelemetry.contrib.php');

    for ($i = 0; $i < 5; $i++) {
        // start a span, register some events
        $timestamp = ClockFactory::getDefault()->now();
        $span = $tracer->spanBuilder('session.generate.span' . microtime(true))->startSpan();
        //startAndActivateSpan('session.generate.span.' . microtime(true));

        $childSpan = $tracer
            ->spanBuilder('child')
            ->setParent($span->storeInContext(Context::getCurrent()))
            ->startSpan();

        // Temporarily setting service name here.  It should eventually be pulled from tracer.resources.
        $span->setAttribute('service.name', 'alwaysOnOTLPGrpcExample');

        $span->setAttribute('remote_ip', '1.2.3.4')
            ->setAttribute('country', 'USA');

        $span->addEvent('found_login' . $i, [
            'id' => $i,
            'username' => 'otuser' . $i,
        ], $timestamp);
        $span->addEvent('generated_session', [
            'id' => md5((string) microtime(true)),
        ], $timestamp);

        // temporarily setting service name here.  It should eventually be pulled from tracer.resources.
        $childSpan->setAttribute('service.name', 'alwaysOnOTLPGrpcExample');

        $childSpan->setAttribute('attr_one', 'one')
            ->setAttribute('attr_two', 'two');

        $childSpan->addEvent('found_event1' . $i, [
            'id' => $i,
            'username' => 'child' . $i,
        ], $timestamp);

        $childSpan->end();
        $span->end();
    }
    echo PHP_EOL . 'OTLPGrpcExample complete!  ';
} else {
    echo PHP_EOL . 'OTLPGrpcExample tracing is not enabled';
}

echo PHP_EOL;
