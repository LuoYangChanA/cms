<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\base;

use craft\errors\VolumeException;
use craft\errors\VolumeObjectNotFoundException;
use craft\models\FieldLayout;
use craft\models\VolumeListing;
use Generator;

/**
 * VolumeInterface defines the common interface to be implemented by volume classes.
 * A class implementing this interface should also use [[SavableComponentTrait]] and [[VolumeTrait]].
 *
 * @mixin VolumeTrait
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
interface VolumeInterface extends SavableComponentInterface
{
    /**
     * Returns the volume's field layout, or `null` if it doesn’t have one.
     *
     * @return FieldLayout|null
     * @since 3.5.0
     */
    public function getFieldLayout(): ?FieldLayout;

    /**
     * Returns the URL to the source, if it’s accessible via HTTP traffic.
     *
     * The URL should end in a `/`.
     *
     * @return string|null The root URL, or `null` if there isn’t one
     */
    public function getRootUrl(): ?string;

    /**
     * List files.
     *
     * @param string $directory The path of the directory to list files of
     * @param bool $recursive whether to fetch file list recursively, defaults to true
     * @return Generator|VolumeListing[]
     * @throws VolumeException
     */
    public function getFileList(string $directory = '', bool $recursive = true): Generator;

    /**
     * Return the file size.
     *
     * @param string $uri
     * @return int
     * @throws VolumeException
     * @since 3.6.0
     */
    public function getFileSize(string $uri): int;

    /**
     * Returns the last time the file was modified.
     *
     * @param string $uri
     * @return int
     * @throws VolumeException
     * @since 3.6.0
     */
    public function getDateModified(string $uri): int;

    /**
     * Creates a file.
     *
     * @param string $path The path of the file, relative to the source’s root
     * @param resource $stream The stream to file
     * @param array $config Additional config options to pass on
     * @throws VolumeException
     * @deprecated in 4.0.0. Use [[writeFileFromStream()]] instead.
     */
    public function createFileByStream(string $path, $stream, array $config): void;

    /**
     * Updates a file.
     *
     * @param string $path The path of the file, relative to the source’s root
     * @param resource $stream The new contents of the file as a stream
     * @param array $config Additional config options to pass on
     * @throws VolumeException
     * @deprecated in 4.0.0.  Use [[writeFileFromStream()]] instead.
     */
    public function updateFileByStream(string $path, $stream, array $config): void;

    /**
     * Writes a file to a volume from a given stream.
     *
     * @param string $path The path of the file, relative to the source’s root
     * @param resource $stream The new contents of the file as a stream
     * @param array $config Additional config options to pass on
     * @throws VolumeException
     */
    public function writeFileFromStream(string $path, $stream, array $config = []): void;

    /**
     * Returns whether a file exists.
     *
     * @param string $path The path of the file, relative to the source’s root
     * @return bool
     * @throws VolumeException
     */
    public function fileExists(string $path): bool;

    /**
     * Deletes a file.
     *
     * @param string $path The path of the file, relative to the source’s root
     */
    public function deleteFile(string $path): void;

    /**
     * Renames a file.
     *
     * @param string $path The old path of the file, relative to the source’s root
     * @param string $newPath The new path of the file, relative to the source’s root
     * @throws VolumeException
     */
    public function renameFile(string $path, string $newPath): void;

    /**
     * Copies a file.
     *
     * @param string $path The path of the file, relative to the source’s root
     * @param string $newPath The path of the new file, relative to the source’s root
     * @throws VolumeException
     */
    public function copyFile(string $path, string $newPath): void;

    /**
     * Save a file from the source's uriPath to a local target path.
     *
     * @param string $uriPath
     * @param string $targetPath
     * @return int amount of bytes copied
     * @deprecated in 4.0.0. Use [[\craft\helpers\Assets::downloadFile()]] instead.
     */
    public function saveFileLocally(string $uriPath, string $targetPath): int;

    /**
     * Gets a stream ready for reading by a file's URI.
     *
     * @param string $uriPath
     * @return resource
     * @throws VolumeException if a stream cannot be created
     */
    public function getFileStream(string $uriPath);

    /**
     * Returns whether a directory exists at the given path.
     *
     * @param string $path The directory path to check
     * @return bool
     * @throws VolumeException
     */
    public function directoryExists(string $path): bool;

    /**
     * Creates a directory.
     *
     * @param string $path The path of the directory, relative to the source’s root
     * @param array $config The config to use
     * @throws VolumeException
     * @since 3.6.0
     */
    public function createDirectory(string $path, array $config = []): void;

    /**
     * Deletes a directory.
     *
     * @param string $path The path of the directory, relative to the source’s root
     * @throws VolumeException
     * @since 3.6.0
     */
    public function deleteDirectory(string $path): void;

    /**
     * Renames a directory.
     *
     * @param string $path The path of the directory, relative to the source’s root
     * @param string $newName The new path of the directory, relative to the source’s root
     * @throws VolumeObjectNotFoundException if directory cannot be found
     * @since 3.6.0
     */
    public function renameDirectory(string $path, string $newName): void;
}
