/* SystemJS module definition */
declare var module: NodeModule;
interface NodeModule {
  id: string;
}

/* IE-specific Navigator extensions */
interface Navigator {
  msSaveOrOpenBlob?: (blob: Blob, filename?: string) => void;
}
