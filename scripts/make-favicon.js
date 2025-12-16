import fs from "fs";
import path from "path";
import sharp from "sharp";
import pngToIco from "png-to-ico";

const input = path.resolve("public/sombrero.png");
const outDir = path.resolve("public");

if (!fs.existsSync(input)) {
  console.error("No existe:", input);
  process.exit(1);
}

async function main() {
  // PNGs pequeños
  const sizes = [
    { name: "favicon-16x16.png", size: 16 },
    { name: "favicon-32x32.png", size: 32 },
    { name: "apple-touch-icon.png", size: 180 },
  ];

  for (const s of sizes) {
    await sharp(input)
      .resize(s.size, s.size, { fit: "contain", background: { r: 0, g: 0, b: 0, alpha: 0 } })
      .png()
      .toFile(path.join(outDir, s.name));
  }

  // ICO (16/32/48)
  const icoPngs = await Promise.all([16, 32, 48].map((size) =>
    sharp(input)
      .resize(size, size, { fit: "contain", background: { r: 0, g: 0, b: 0, alpha: 0 } })
      .png()
      .toBuffer()
  ));

  const ico = await pngToIco(icoPngs);
  fs.writeFileSync(path.join(outDir, "favicon.ico"), ico);

  console.log("Favicons generados en /public");
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
