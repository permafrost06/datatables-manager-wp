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

const stripPattern = async (file, pattern) => {
  const fileContent = await readFile(file, "utf-8");

  const newContent = fileContent.replace(pattern, "");

  return newContent;
};

const fileTypes = ["php"];

for (const file of files) {
  const fileExt = file.split(".")[file.split(".").length - 1];

  if (fileTypes.indexOf(fileExt) >= 0) {
    const strippedContent = await stripPattern(
      file,
      /$\s*\/\* debug-start \*\/.*?\/\* debug-end \*\//gms
    );

    await writeFile(file, strippedContent, "utf-8");
  }
}
