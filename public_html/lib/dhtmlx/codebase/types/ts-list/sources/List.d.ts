import { DataCollection, DataEvents, DragEvents, IDataEventsHandlersMap, IDragEventsHandlersMap } from "../../ts-data";
import { VNode } from "../../ts-common/dom";
import { IEventSystem } from "../../ts-common/events";
import { IHandlers } from "../../ts-common/types";
import { View } from "../../ts-common/view";
import { IList, IListConfig, IListEventHandlersMap, IListItem, ISelection, ListEvents } from "./types";
export declare class List extends View implements IList {
    config: IListConfig;
    data: DataCollection;
    events: IEventSystem<DataEvents | ListEvents | DragEvents, IListEventHandlersMap & IDataEventsHandlersMap & IDragEventsHandlersMap>;
    selection: ISelection;
    protected _handlers: IHandlers;
    private _range;
    private _visibleHeight;
    private _topOffset;
    private _edited;
    private _navigationDestructor;
    private _widgetInFocus;
    private _documentClickDestuctor;
    private _focusIndex;
    private _disabledSelection;
    constructor(node: HTMLElement | string, config?: IListConfig);
    disableSelection(): void;
    enableSelection(): void;
    editItem(id: string): void;
    getFocusItem(): any;
    setFocus(id: string): void;
    getFocus(): string;
    destructor(): void;
    getFocusIndex(): number;
    setFocusIndex(index: number): void;
    edit(id: string): void;
    protected _renderItem(item: IListItem, index: number): VNode;
    protected _renderAsHtml(html: string, item: IListItem, focus: boolean): VNode;
    protected _renderAsValue(item: IListItem, focus: boolean): VNode;
    protected _renderList(): VNode;
    protected _renderVirtualList(): VNode;
    private _setFocusIndex;
    private _updateVirtual;
    private _getHeight;
    private _getHotkeys;
    private _lazyLoad;
}
