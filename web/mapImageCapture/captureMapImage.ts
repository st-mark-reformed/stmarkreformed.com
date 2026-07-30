import { writeFile } from 'node:fs/promises';
import path from 'node:path';
import { chromium, type Frame } from 'playwright';

type CaptureMapImageOptions = {
    streetAddress: string;
    outputFileName: string;
    viewportWidth: number;
    viewportHeight: number;
    deviceScaleFactor: number;
    tileSettleDelayMs: number;
};

const DEFAULT_VIEWPORT_WIDTH = 1920;
const DEFAULT_VIEWPORT_HEIGHT = 960;
const DEFAULT_DEVICE_SCALE_FACTOR = 2;
const DEFAULT_TILE_SETTLE_DELAY_MS = 5000;

function parseArgs(): CaptureMapImageOptions {
    const args = new Map<string, string>();

    for (let index = 2; index < process.argv.length; index += 2) {
        const flag = process.argv[index];
        const value = process.argv[index + 1];

        if (!flag?.startsWith('--') || value === undefined) {
            throw new Error(`Unexpected argument: ${flag}`);
        }

        args.set(flag.slice(2), value);
    }

    const streetAddress = args.get('street-address');
    const output = args.get('output');

    if (!streetAddress || !output) {
        throw new Error(
            'Usage: pnpm capture-map-image --street-address "1021 Windcross Ct, Franklin, TN 37067" --output fcs-map-image.png',
        );
    }

    const outputFileName = path.extname(output).toLowerCase() === '.png' ? output : `${output}.png`;

    return {
        streetAddress,
        outputFileName,
        viewportWidth: Number(args.get('width') ?? DEFAULT_VIEWPORT_WIDTH),
        viewportHeight: Number(args.get('height') ?? DEFAULT_VIEWPORT_HEIGHT),
        deviceScaleFactor: Number(args.get('scale') ?? DEFAULT_DEVICE_SCALE_FACTOR),
        tileSettleDelayMs: Number(args.get('delay') ?? DEFAULT_TILE_SETTLE_DELAY_MS),
    };
}

// Google renders the address info card inside a closed shadow root, so
// querySelector from outside the frame can't reach it. elementFromPoint
// does a hit-test against the rendered tree instead, which pierces closed
// shadow roots and hands back a real node we can hide.
async function removeInfoCard(mapFrame: Frame): Promise<boolean> {
    return mapFrame.evaluate(() => {
        let target = document.elementFromPoint(20, 20);

        if (!target) {
            return false;
        }

        while (target.parentElement && getComputedStyle(target).position !== 'absolute') {
            target = target.parentElement;
        }

        (target as HTMLElement).style.display = 'none';

        return true;
    });
}

async function captureMapImage(options: CaptureMapImageOptions): Promise<void> {
    const browser = await chromium.launch();
    const context = await browser.newContext({
        viewport: { width: options.viewportWidth, height: options.viewportHeight },
        deviceScaleFactor: options.deviceScaleFactor,
    });
    const page = await context.newPage();

    const embedUrl = `https://www.google.com/maps?q=${encodeURIComponent(options.streetAddress)}&output=embed`;

    // Google refuses to render the embed URL unless it's actually inside an
    // iframe, so we wrap it in a minimal host page sized to the target image.
    await page.setContent(
        `
        <html>
        <head><style>html,body{margin:0;padding:0;}iframe{border:0;display:block;}</style></head>
        <body>
            <iframe src="${embedUrl}" width="${options.viewportWidth}" height="${options.viewportHeight}"></iframe>
        </body>
        </html>
        `,
        { waitUntil: 'networkidle' },
    );

    // The embed keeps streaming in tiles/labels for a bit after network idle.
    await new Promise((resolve) => {
        setTimeout(resolve, options.tileSettleDelayMs);
    });

    const mapFrame = page.frames().find((frame) => frame.url().includes('google.com/maps'));

    if (!mapFrame) {
        throw new Error('Could not find the Google Maps embed frame.');
    }

    await removeInfoCard(mapFrame);

    const outputPath = path.join(process.cwd(), 'public', 'images', 'home', options.outputFileName);
    const screenshot = await page.screenshot();

    await writeFile(outputPath, screenshot);

    await browser.close();

    console.log(`Saved map image to ${outputPath}`);
}

captureMapImage(parseArgs()).catch((error: unknown) => {
    console.error(error);
    process.exitCode = 1;
});
