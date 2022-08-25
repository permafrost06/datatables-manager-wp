const fs = require("fs");
const archiver = require("archiver");
const path = require("path");

const assetFile = path.join(__dirname, "includes", "Assets.php");

fs.copyFileSync(assetFile, "Assets_copy.php");

const content = fs.readFileSync(assetFile, "utf-8");

const removed = content.replace(
  /\/\* next-line-enables-hmr \*\/\s+.*$\s+\/\* next-line-disables-hmr \*\/\s+\/\/\s+/gm,
  ""
);

const change_version = removed.replace(
  /filemtime.+$/gm,
  "DATATABLES_MANAGER_VERSION,"
);

fs.writeFileSync(assetFile, change_version, "utf8");

const dir = path.resolve(path.join(__dirname, "dist"));

if (!fs.existsSync(dir)) {
  fs.mkdirSync(dir);
}

const output = fs.createWriteStream("dist/datatables-manager.zip");
const archive = archiver("zip");

output.on("close", function () {
  console.log(archive.pointer() + " total bytes");
  console.log(
    "archiver has been finalized and the output file descriptor has closed."
  );

  fs.copyFileSync("Assets_copy.php", assetFile);
  fs.unlinkSync("Assets_copy.php");
});

archive.on("error", function (err) {
  throw err;
});

archive.pipe(output);

archive.directory("assets");
archive.directory("includes");
archive.directory("vendor");

archive.file("datatables-manager.php");

archive.finalize();
