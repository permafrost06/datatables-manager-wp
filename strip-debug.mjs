import { readdir, readFile, writeFile } from "node:fs/promises";
import { join } from "node:path";

const readdirRecursive = async (dir, excludedDirs) => {
  if (excludedDirs.indexOf(dir) < 0) {
    const files = await readdir(dir, { withFileTypes: true });

    const paths = files.map(async (file) => {
      const path = join(dir, file.name);

      if (file.isDirectory()) return await readdirRecursive(path, excludedDirs);

      return path;
    });

    return (await Promise.all(paths)).flat(Infinity);
  } else {
    return [];
  }
};

const excludedDirs = ["node_modules", ".git", ".vscode", "vendor"];

const files = await readdirRecursive(".", excludedDirs);

const stripDebug = async (file) => {
  const fileContent = await readFile(file, "utf-8");

  const regex = /$\s*\/\* debug-start \*\/.*?\/\* debug-end \*\//gms;
  const newContent = fileContent.replace(regex, "");

  return newContent;
};

const fileTypes = ["php"];

for (const file of files) {
  const fileExt = file.split(".")[file.split(".").length - 1];

  if (fileTypes.indexOf(fileExt) >= 0) {
    const strippedContent = await stripDebug(file);

    await writeFile(file, strippedContent, "utf-8");
  }
}
