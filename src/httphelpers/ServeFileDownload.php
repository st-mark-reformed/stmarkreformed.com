<?php

namespace src\httphelpers;

use craft\web\Request;
use DaveRandom\Resume\FileResource;
use DaveRandom\Resume\InvalidRangeHeaderException;
use DaveRandom\Resume\NonExistentFileException;
use DaveRandom\Resume\RangeSet;
use DaveRandom\Resume\SendFileFailureException;
use DaveRandom\Resume\UnreadableFileException;
use DaveRandom\Resume\UnsatisfiableRangeException;
use src\downloads\ResourceServlet;
use yii\web\HttpException;

class ServeFileDownload
{
    /**
     * @param string $fullServerPath
     * @param Request $request
     * @param string|null $mimeType
     * @throws HttpException
     */
    public function serve(
        string $fullServerPath,
        Request $request,
        $mimeType = null
    ) {
        try {
            $rangeHeader = (string) ($_SERVER['HTTP_RANGE'] ?? '');

            $rangeSet = null;

            if ($rangeHeader !== '') {
                $rangeSet = RangeSet::createFromHeader($rangeHeader);
            }

            $resource = new FileResource(
                $fullServerPath,
                $mimeType
            );

            $servlet = new ResourceServlet($resource);

            $servlet->sendResource(
                $rangeSet,
                null,
                $request
            );

            exit;
        } catch (InvalidRangeHeaderException $e) {
            throw new HttpException(
                400,
                'Bad Request'
            );
        } catch (UnsatisfiableRangeException $e) {
            throw new HttpException(
                416,
                'Range Not Satisfiable'
            );
        } catch (NonExistentFileException $e) {
            throw new HttpException(
                404,
                'Not Found'
            );
        } catch (UnreadableFileException $e) {
            throw new HttpException(
                500,
                'Internal Server Error'
            );
        } catch (SendFileFailureException $e) {
            throw new HttpException(
                500,
                'Internal Server Error'
            );
        }

        throw new HttpException(
            404,
            'Not Found'
        );
    }
}
